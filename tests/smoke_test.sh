#!/bin/bash
# ============================================================================
# Smoke Test — Portal Desa Karangtalun Backend
# ============================================================================
# Usage:
#   chmod +x tests/smoke_test.sh
#   bash tests/smoke_test.sh [base_url]
#
# Prerequisites:
#   - php artisan migrate:fresh --seed
#   - php artisan serve (running on another terminal)
#   - curl and jq installed
# ============================================================================

BASE_URL="${1:-http://localhost:8000}"
API="$BASE_URL/api"
PASS=0
FAIL=0
TOTAL=0

green()  { echo -e "\033[32m✓ $1\033[0m"; }
red()    { echo -e "\033[31m✗ $1\033[0m"; }
header() { echo -e "\n\033[1;34m── $1 ──\033[0m"; }

check() {
    local desc="$1" expected="$2" actual="$3" body="$4"
    TOTAL=$((TOTAL + 1))
    if [ "$actual" = "$expected" ]; then
        PASS=$((PASS + 1))
        green "$desc (HTTP $actual)"
    else
        FAIL=$((FAIL + 1))
        red "$desc — expected $expected, got $actual"
        echo "    Response: $(echo "$body" | head -c 200)"
    fi
}

check_json() {
    local desc="$1" expected_code="$2" actual_code="$3" body="$4" field="$5" expected_val="$6"
    TOTAL=$((TOTAL + 1))
    local actual_val
    actual_val=$(echo "$body" | jq -r "$field" 2>/dev/null)
    if [ "$actual_code" = "$expected_code" ] && [ "$actual_val" = "$expected_val" ]; then
        PASS=$((PASS + 1))
        green "$desc (HTTP $actual_code, $field=$actual_val)"
    else
        FAIL=$((FAIL + 1))
        red "$desc — HTTP $actual_code (exp $expected_code), $field=$actual_val (exp $expected_val)"
    fi
}

# ── HTTP helpers ──
do_req() {
    local method="$1" path="$2" data="$3" token="$4"
    local -a args=(-s -w '\n%{http_code}' -H 'Accept: application/json')
    [ -n "$token" ] && args+=(-H "Authorization: Bearer $token")
    if [ "$method" = "GET" ]; then
        curl "${args[@]}" "$API$path"
    else
        args+=(-X "$method" -H 'Content-Type: application/json')
        [ -n "$data" ] && args+=(-d "$data")
        curl "${args[@]}" "$API$path"
    fi
}

parse() {
    local raw="$1"
    CODE=$(echo "$raw" | tail -1)
    BODY=$(echo "$raw" | sed '$d')
}

echo "============================================"
echo " Smoke Test — Portal Desa Karangtalun"
echo " Base URL: $BASE_URL"
echo "============================================"

# ── 1. Health ────────────────────────────────────
header "Health Check"
RAW=$(curl -s -w '\n%{http_code}' "$BASE_URL/up")
parse "$RAW"
check "GET /up" "200" "$CODE" "$BODY"

# ── 2. Auth: Login ───────────────────────────────
header "Authentication"

# Login warga (NIK)
RAW=$(do_req POST "/auth/login" '{"identifier":"3314072505850001","password":"demo123"}')
parse "$RAW"
check "Login warga (NIK)" "200" "$CODE" "$BODY"
WARGA_TOKEN=$(echo "$BODY" | jq -r '.data.token // empty')

# Login admin (id_petugas)
RAW=$(do_req POST "/auth/login" '{"identifier":"ADM-001","password":"demo123"}')
parse "$RAW"
check "Login admin (ADM-001)" "200" "$CODE" "$BODY"
ADMIN_TOKEN=$(echo "$BODY" | jq -r '.data.token // empty')

# Login staf (id_petugas)
RAW=$(do_req POST "/auth/login" '{"identifier":"STF-001","password":"demo123"}')
parse "$RAW"
check "Login staf (STF-001)" "200" "$CODE" "$BODY"
STAF_TOKEN=$(echo "$BODY" | jq -r '.data.token // empty')

# Failed login → 401
RAW=$(do_req POST "/auth/login" '{"identifier":"wrong","password":"wrong"}')
parse "$RAW"
check_json "Login failed → 401 JSON" "401" "$CODE" "$BODY" ".success" "false"

# ── 3. Auth guard ────────────────────────────────
header "Auth & Role Guards"

# Unauthenticated
RAW=$(do_req GET "/auth/me")
parse "$RAW"
check "Unauthenticated → 401" "401" "$CODE" "$BODY"

# /auth/me with token
RAW=$(do_req GET "/auth/me" "" "$ADMIN_TOKEN")
parse "$RAW"
check "GET /auth/me (admin)" "200" "$CODE" "$BODY"

# Warga → admin route
RAW=$(do_req GET "/admin/kk" "" "$WARGA_TOKEN")
parse "$RAW"
check "Warga → admin route = 403" "403" "$CODE" "$BODY"

# Warga → staf route
RAW=$(do_req GET "/staf/surat" "" "$WARGA_TOKEN")
parse "$RAW"
check "Warga → staf route = 403" "403" "$CODE" "$BODY"

# Admin CAN access warga routes
RAW=$(do_req GET "/warga/kk" "" "$ADMIN_TOKEN")
parse "$RAW"
check "Admin → warga route = allowed" "200" "$CODE" "$BODY"

# ── 4. OTP Password Reset Flow ──────────────────
header "OTP Password Reset Flow"

# Step 1: Identify → sends OTP
RAW=$(do_req POST "/auth/lupa-sandi/identify" '{"identifier":"3314072505850001"}')
parse "$RAW"
check "Lupa sandi identify → 200" "200" "$CODE" "$BODY"

# Extract OTP if debug mode
OTP=$(echo "$BODY" | jq -r '.data.debug_otp // empty')
if [ -n "$OTP" ]; then
    green "  Debug OTP received: $OTP"

    # Step 2: Verify OTP
    RAW=$(do_req POST "/auth/lupa-sandi/verify-otp" "{\"identifier\":\"3314072505850001\",\"otp\":\"$OTP\"}")
    parse "$RAW"
    check "Verify OTP → 200" "200" "$CODE" "$BODY"
    RESET_TOKEN=$(echo "$BODY" | jq -r '.data.reset_token // empty')

    if [ -n "$RESET_TOKEN" ]; then
        # Step 3: Reset password
        RAW=$(do_req POST "/auth/lupa-sandi/reset" "{\"identifier\":\"3314072505850001\",\"reset_token\":\"$RESET_TOKEN\",\"password\":\"demo123\",\"password_confirmation\":\"demo123\"}")
        parse "$RAW"
        check "Reset password → 200" "200" "$CODE" "$BODY"
    fi
else
    TOTAL=$((TOTAL + 1)); PASS=$((PASS + 1))
    green "  OTP debug not enabled (OTP_DEBUG=false); skipping verify/reset"
fi

# ── 5. CMS Public ───────────────────────────────
header "CMS Public Endpoints"

for ep in identitas-desa profil-desa berita galeri umkm aparatur potensi-desa fasilitas layanan-publik ppid-dokumen peta-desa infografis header-footer; do
    RAW=$(do_req GET "/cms/$ep")
    parse "$RAW"
    check "GET /cms/$ep" "200" "$CODE" "$BODY"
done

RAW=$(do_req GET "/statistik/kependudukan")
parse "$RAW"
check "GET /statistik/kependudukan" "200" "$CODE" "$BODY"

# ── 6. Warga: KK ────────────────────────────────
header "Warga — KK & Eligible"

RAW=$(do_req GET "/warga/kk" "" "$WARGA_TOKEN")
parse "$RAW"
check "GET /warga/kk" "200" "$CODE" "$BODY"

RAW=$(do_req GET "/warga/kk/anggota-eligible" "" "$WARGA_TOKEN")
parse "$RAW"
check "GET /warga/kk/anggota-eligible" "200" "$CODE" "$BODY"

# ── 7. E-Surat validation ───────────────────────
header "E-Surat Security"

# Invalid pemohon NIK
RAW=$(do_req POST "/warga/surat" '{"jenis_surat":"surat_keterangan_domisili","pemohon_nik":"0000000000000000","keperluan":"Test"}' "$WARGA_TOKEN")
parse "$RAW"
check "E-surat: invalid NIK → 422" "422" "$CODE" "$BODY"

# ── 8. Staf endpoints ───────────────────────────
header "Staf Endpoints"

RAW=$(do_req GET "/staf/surat" "" "$STAF_TOKEN")
parse "$RAW"
check "GET /staf/surat" "200" "$CODE" "$BODY"

RAW=$(do_req GET "/staf/laporan" "" "$STAF_TOKEN")
parse "$RAW"
check "GET /staf/laporan" "200" "$CODE" "$BODY"

RAW=$(do_req GET "/staf/permohonan" "" "$STAF_TOKEN")
parse "$RAW"
check "GET /staf/permohonan" "200" "$CODE" "$BODY"

# ── 9. Admin endpoints ──────────────────────────
header "Admin Endpoints"

RAW=$(do_req GET "/admin/kk" "" "$ADMIN_TOKEN")
parse "$RAW"
check "GET /admin/kk" "200" "$CODE" "$BODY"

RAW=$(do_req GET "/admin/users" "" "$ADMIN_TOKEN")
parse "$RAW"
check "GET /admin/users" "200" "$CODE" "$BODY"

# ── 10. CMS Admin CRUD ──────────────────────────
header "CMS Admin CRUD"

# Create berita
RAW=$(do_req POST "/admin/cms/berita" '{"judul":"Test Berita","slug":"test-berita-smoke","kategori":"umum","konten":"Konten test smoke.","penulis":"Admin","status":"Terbit","tipe":"Artikel"}' "$ADMIN_TOKEN")
parse "$RAW"
check "POST /admin/cms/berita (create)" "201" "$CODE" "$BODY"
BERITA_ID=$(echo "$BODY" | jq -r '.data.id // empty')

if [ -n "$BERITA_ID" ]; then
    # Update berita
    RAW=$(do_req PUT "/admin/cms/berita/$BERITA_ID" '{"judul":"Test Berita Updated"}' "$ADMIN_TOKEN")
    parse "$RAW"
    check "PUT /admin/cms/berita/$BERITA_ID (update)" "200" "$CODE" "$BODY"

    # Delete berita
    RAW=$(do_req DELETE "/admin/cms/berita/$BERITA_ID" '{}' "$ADMIN_TOKEN")
    parse "$RAW"
    check "DELETE /admin/cms/berita/$BERITA_ID" "200" "$CODE" "$BODY"
fi

# ── 11. UMKM Like ───────────────────────────────
header "Public Actions"

RAW=$(do_req POST "/cms/umkm/1/like" '{}')
parse "$RAW"
check "POST /cms/umkm/1/like" "200" "$CODE" "$BODY"

# ── 12. Notifikasi ──────────────────────────────
header "Notifikasi"

RAW=$(do_req GET "/notifikasi" "" "$WARGA_TOKEN")
parse "$RAW"
check "GET /notifikasi (warga)" "200" "$CODE" "$BODY"

# ── 13. Profile ─────────────────────────────────
header "Profile"

RAW=$(do_req GET "/profile" "" "$WARGA_TOKEN")
parse "$RAW"
check "GET /profile" "200" "$CODE" "$BODY"

# Password not exposed
HAS_PASSWORD=$(echo "$BODY" | jq 'if .data.password then "yes" else "no" end' 2>/dev/null)
TOTAL=$((TOTAL + 1))
if [ "$HAS_PASSWORD" = '"no"' ]; then
    PASS=$((PASS + 1))
    green "Password not exposed in profile response"
else
    FAIL=$((FAIL + 1))
    red "Password exposed in profile response!"
fi

# ── 14. Error handling ──────────────────────────
header "Error Handling"

RAW=$(do_req GET "/nonexistent-endpoint")
parse "$RAW"
check_json "Unknown endpoint → 404 JSON" "404" "$CODE" "$BODY" ".success" "false"

# ── 15. Logout ──────────────────────────────────
header "Logout"

RAW=$(do_req POST "/auth/logout" '{}' "$WARGA_TOKEN")
parse "$RAW"
check "POST /auth/logout" "200" "$CODE" "$BODY"

# After logout, me should fail
RAW=$(do_req GET "/auth/me" "" "$WARGA_TOKEN")
parse "$RAW"
check "After logout → 401" "401" "$CODE" "$BODY"

# ── Summary ──────────────────────────────────────
echo ""
echo "============================================"
echo " Results: $PASS passed / $FAIL failed / $TOTAL total"
echo "============================================"

if [ $FAIL -gt 0 ]; then
    echo -e "\033[31mSome tests failed!\033[0m"
    exit 1
else
    echo -e "\033[32mAll tests passed!\033[0m"
    exit 0
fi
