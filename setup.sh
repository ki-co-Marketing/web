#!/usr/bin/env bash
# =============================================================================
# Urban CMS — Docker local environment setup
# Run once after: docker compose up -d
# =============================================================================
set -euo pipefail

WP="docker compose run --rm wpcli wp --allow-root"
SITE_URL="http://localhost:8080"

# Colour helpers
GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RESET='\033[0m'
info()    { echo -e "${YELLOW}→ $*${RESET}"; }
success() { echo -e "${GREEN}✓ $*${RESET}"; }

# ─── Wait for WordPress to be ready ──────────────────────────────────────────
info "Waiting for WordPress container to be ready..."
until docker compose run --rm wpcli wp --allow-root db check --quiet 2>/dev/null; do
  sleep 3
done
success "Database is ready"

# ─── Install WordPress ────────────────────────────────────────────────────────
info "Installing WordPress..."
if $WP core is-installed 2>/dev/null; then
  success "WordPress already installed — skipping core install"
else
  $WP core install \
    --url="$SITE_URL" \
    --title="Urban CMS Platform" \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@example.com \
    --skip-email
  success "WordPress installed (admin / admin)"
fi

# ─── Activate theme ───────────────────────────────────────────────────────────
info "Activating Urban CMS theme..."
$WP theme activate urban-cms
success "Theme activated"

# ─── Permalinks ───────────────────────────────────────────────────────────────
info "Setting permalinks..."
$WP rewrite structure '/%postname%/' --hard
$WP rewrite flush --hard
success "Permalinks set to /%postname%/"

# ─── Disable comments site-wide ───────────────────────────────────────────────
$WP option update default_comment_status closed
$WP option update default_ping_status closed

# ─── Delete default content ───────────────────────────────────────────────────
info "Removing default sample content..."
$WP post delete 1 2 --force 2>/dev/null || true   # Hello World post + Sample page

# ─── Create pages ─────────────────────────────────────────────────────────────
info "Creating pages..."

HOME_ID=$($WP post create \
  --post_type=page \
  --post_status=publish \
  --post_title="Home" \
  --post_content="" \
  --porcelain)

NEWS_ID=$($WP post create \
  --post_type=page \
  --post_status=publish \
  --post_title="News" \
  --post_content="" \
  --porcelain)

SUBMIT_BIZ_ID=$($WP post create \
  --post_type=page \
  --post_status=publish \
  --post_title="Submit a Business" \
  --post_content="" \
  --porcelain)
$WP post meta update "$SUBMIT_BIZ_ID" _wp_page_template "templates/page-submit-business.php"

SUBMIT_EVENT_ID=$($WP post create \
  --post_type=page \
  --post_status=publish \
  --post_title="Submit an Event" \
  --post_content="" \
  --porcelain)
$WP post meta update "$SUBMIT_EVENT_ID" _wp_page_template "templates/page-submit-event.php"

DASHBOARD_ID=$($WP post create \
  --post_type=page \
  --post_status=publish \
  --post_title="Member Dashboard" \
  --post_content="" \
  --porcelain)
$WP post meta update "$DASHBOARD_ID" _wp_page_template "templates/page-dashboard.php"

# Set static front page
$WP option update show_on_front page
$WP option update page_on_front "$HOME_ID"
$WP option update page_for_posts "$NEWS_ID"

success "Pages created (Home, News, Submit Business, Submit Event, Dashboard)"

# ─── Navigation menu ──────────────────────────────────────────────────────────
info "Creating navigation menu..."
$WP menu create "Primary Menu"
$WP menu location assign "Primary Menu" primary
$WP menu item add-post "Primary Menu" "$HOME_ID"
$WP menu item add-post "Primary Menu" "$NEWS_ID"
$WP menu item add-post "Primary Menu" "$SUBMIT_BIZ_ID"
$WP menu item add-post "Primary Menu" "$SUBMIT_EVENT_ID"
$WP menu item add-post "Primary Menu" "$DASHBOARD_ID"
success "Navigation menu created"

# ─── Taxonomies ───────────────────────────────────────────────────────────────
info "Creating taxonomy terms..."

$WP term create event_category "Arts & Culture"   --slug=arts-culture   --porcelain > /dev/null
$WP term create event_category "Business"          --slug=business       --porcelain > /dev/null
$WP term create event_category "Community"         --slug=community      --porcelain > /dev/null
$WP term create event_category "Food & Drink"      --slug=food-drink     --porcelain > /dev/null

$WP term create business_category "Retail"         --slug=retail         --porcelain > /dev/null
$WP term create business_category "Food & Dining"  --slug=food-dining    --porcelain > /dev/null
$WP term create business_category "Services"       --slug=services       --porcelain > /dev/null
$WP term create business_category "Health"         --slug=health         --porcelain > /dev/null

$WP term create district_neighborhood "Downtown Core"  --slug=downtown-core   --porcelain > /dev/null
$WP term create district_neighborhood "Waterfront"     --slug=waterfront      --porcelain > /dev/null
$WP term create district_neighborhood "Arts District"  --slug=arts-district   --porcelain > /dev/null

$WP term create initiative_type "Infrastructure"   --slug=infrastructure  --porcelain > /dev/null
$WP term create initiative_type "Public Safety"    --slug=public-safety   --porcelain > /dev/null
$WP term create initiative_type "Economic Dev"     --slug=economic-dev    --porcelain > /dev/null

success "Taxonomy terms created"

# ─── Sample Events ────────────────────────────────────────────────────────────
info "Creating sample events..."

E1=$($WP post create \
  --post_type=urban_event \
  --post_status=publish \
  --post_title="Downtown Summer Market" \
  --post_content="Join us for the annual summer market featuring local vendors, live music, and family activities throughout the downtown core." \
  --post_excerpt="Annual summer market with local vendors and live music." \
  --porcelain)
$WP post meta update "$E1" _event_start_date "2026-06-14"
$WP post meta update "$E1" _event_end_date   "2026-06-14"
$WP post meta update "$E1" _event_start_time "10:00"
$WP post meta update "$E1" _event_end_time   "18:00"
$WP post meta update "$E1" _event_location   "Central Plaza"
$WP post meta update "$E1" _event_address    "123 Main Street, Downtown"
$WP post meta update "$E1" _event_cost       "Free"
$WP post meta update "$E1" _event_organizer  "Downtown BID"
$WP post set-terms "$E1" community event_category

E2=$($WP post create \
  --post_type=urban_event \
  --post_status=publish \
  --post_title="Small Business Networking Night" \
  --post_content="Connect with fellow downtown business owners, share ideas, and learn about new district programs at our quarterly networking event." \
  --post_excerpt="Quarterly networking event for downtown business owners." \
  --porcelain)
$WP post meta update "$E2" _event_start_date "2026-07-08"
$WP post meta update "$E2" _event_end_date   "2026-07-08"
$WP post meta update "$E2" _event_start_time "18:00"
$WP post meta update "$E2" _event_end_time   "20:00"
$WP post meta update "$E2" _event_location   "The Atrium"
$WP post meta update "$E2" _event_address    "456 Commerce Ave, Downtown"
$WP post meta update "$E2" _event_cost       "\$15"
$WP post meta update "$E2" _event_organizer  "Downtown BID"
$WP post set-terms "$E2" business event_category

E3=$($WP post create \
  --post_type=urban_event \
  --post_status=publish \
  --post_title="Waterfront Art Walk" \
  --post_content="Explore local art installations along the waterfront promenade. Meet artists, enjoy live performances, and discover the district's creative community." \
  --post_excerpt="Art installations and live performances along the waterfront." \
  --porcelain)
$WP post meta update "$E3" _event_start_date "2026-07-19"
$WP post meta update "$E3" _event_end_date   "2026-07-19"
$WP post meta update "$E3" _event_start_time "14:00"
$WP post meta update "$E3" _event_end_time   "20:00"
$WP post meta update "$E3" _event_location   "Waterfront Promenade"
$WP post meta update "$E3" _event_address    "Waterfront Dr, Downtown"
$WP post meta update "$E3" _event_cost       "Free"
$WP post meta update "$E3" _event_organizer  "Arts District Council"
$WP post set-terms "$E3" arts-culture event_category

success "3 sample events created"

# ─── Sample Businesses ────────────────────────────────────────────────────────
info "Creating sample businesses..."

B1=$($WP post create \
  --post_type=urban_business \
  --post_status=publish \
  --post_title="The Corner Roastery" \
  --post_content="Specialty coffee roastery and café serving single-origin beans sourced directly from sustainable farms. Our rotating menu features pour-overs, espresso drinks, and house-made pastries." \
  --post_excerpt="Specialty coffee roastery with single-origin beans and house-made pastries." \
  --porcelain)
$WP post meta update "$B1" _business_address  "88 Main St"
$WP post meta update "$B1" _business_phone    "(206) 555-0142"
$WP post meta update "$B1" _business_email    "hello@cornerroastery.example.com"
$WP post meta update "$B1" _business_website  "https://cornerroastery.example.com"
$WP post meta update "$B1" _business_hours    "Mon–Fri 6am–6pm · Sat–Sun 7am–5pm"
$WP post meta update "$B1" _business_lat      "47.6062"
$WP post meta update "$B1" _business_lng      "-122.3321"
$WP post meta update "$B1" _business_featured "1"
$WP post set-terms "$B1" food-dining business_category
$WP post set-terms "$B1" downtown-core district_neighborhood

B2=$($WP post create \
  --post_type=urban_business \
  --post_status=publish \
  --post_title="Civic Books & Records" \
  --post_content="Independent bookstore and record shop celebrating local authors and musicians. Browse our curated selection of new releases, rare finds, and district history titles." \
  --post_excerpt="Independent bookstore and record shop celebrating local talent." \
  --porcelain)
$WP post meta update "$B2" _business_address  "214 Commerce Ave"
$WP post meta update "$B2" _business_phone    "(206) 555-0187"
$WP post meta update "$B2" _business_email    "info@civicbooks.example.com"
$WP post meta update "$B2" _business_website  "https://civicbooks.example.com"
$WP post meta update "$B2" _business_hours    "Mon–Sat 10am–8pm · Sun 11am–6pm"
$WP post meta update "$B2" _business_lat      "47.6072"
$WP post meta update "$B2" _business_lng      "-122.3341"
$WP post meta update "$B2" _business_featured "0"
$WP post set-terms "$B2" retail business_category
$WP post set-terms "$B2" arts-district district_neighborhood

B3=$($WP post create \
  --post_type=urban_business \
  --post_status=publish \
  --post_title="Harbor View Wellness" \
  --post_content="Full-service wellness studio offering yoga, massage, and holistic health consultations. Our certified practitioners support your wellbeing in a calm, welcoming space." \
  --post_excerpt="Yoga, massage, and holistic health with harbour views." \
  --porcelain)
$WP post meta update "$B3" _business_address  "501 Waterfront Dr, Suite 3"
$WP post meta update "$B3" _business_phone    "(206) 555-0221"
$WP post meta update "$B3" _business_email    "bookings@harborviewwellness.example.com"
$WP post meta update "$B3" _business_website  "https://harborviewwellness.example.com"
$WP post meta update "$B3" _business_hours    "Tue–Sun 8am–7pm"
$WP post meta update "$B3" _business_lat      "47.6041"
$WP post meta update "$B3" _business_lng      "-122.3361"
$WP post meta update "$B3" _business_featured "1"
$WP post set-terms "$B3" health business_category
$WP post set-terms "$B3" waterfront district_neighborhood

success "3 sample businesses created"

# ─── Sample Initiatives ───────────────────────────────────────────────────────
info "Creating sample initiatives..."

I1=$($WP post create \
  --post_type=urban_initiative \
  --post_status=publish \
  --post_title="Streetscape Improvement Project" \
  --post_content="A comprehensive upgrade of Main Street's pedestrian infrastructure including new street furniture, improved lighting, wider sidewalks, and native planting. This multi-year initiative will transform the core corridor into a more welcoming, accessible, and commercially vibrant destination." \
  --post_excerpt="Upgrading Main Street with new lighting, furniture, and native planting." \
  --porcelain)
$WP post meta update "$I1" _initiative_status   "active"
$WP post meta update "$I1" _initiative_progress "65"
$WP post meta update "$I1" _initiative_budget   "\$2.4M"
$WP post meta update "$I1" _initiative_start    "2025-03-01"
$WP post meta update "$I1" _initiative_end      "2026-09-30"
$WP post meta update "$I1" _initiative_lead     "City Public Works + Downtown BID"
$WP post meta update "$I1" _initiative_location "Main Street (1st Ave to 6th Ave)"
$WP post set-terms "$I1" infrastructure initiative_type

I2=$($WP post create \
  --post_type=urban_initiative \
  --post_status=publish \
  --post_title="Safe Streets Ambassador Program" \
  --post_content="Deploying trained community ambassadors across the downtown core to provide visitor assistance, deter anti-social behaviour, and connect individuals in need with support services. Ambassadors operate seven days a week during peak hours." \
  --post_excerpt="Community ambassadors providing support and safety across the downtown core." \
  --porcelain)
$WP post meta update "$I2" _initiative_status   "active"
$WP post meta update "$I2" _initiative_progress "80"
$WP post meta update "$I2" _initiative_budget   "\$680K / year"
$WP post meta update "$I2" _initiative_start    "2024-09-01"
$WP post meta update "$I2" _initiative_end      "2027-08-31"
$WP post meta update "$I2" _initiative_lead     "Downtown BID Safety Team"
$WP post meta update "$I2" _initiative_location "Downtown Core (full district)"
$WP post set-terms "$I2" public-safety initiative_type

success "2 sample initiatives created"

# ─── Sample Team Members ──────────────────────────────────────────────────────
info "Creating sample team members..."

T1=$($WP post create \
  --post_type=urban_team \
  --post_status=publish \
  --post_title="Sarah Chen" \
  --post_content="Sarah has led downtown revitalisation efforts for over 12 years, specialising in public-private partnerships and place-based economic development strategies. She holds a Master's in Urban Planning from the University of Washington." \
  --post_excerpt="Executive Director with 12 years of downtown revitalisation experience." \
  --porcelain)
$WP post meta update "$T1" _team_title    "Executive Director"
$WP post meta update "$T1" _team_email    "schen@urbandistrict.example.com"
$WP post meta update "$T1" _team_phone    "(206) 555-0100"
$WP post meta update "$T1" _team_linkedin "https://linkedin.com/in/sarahchen-example"

T2=$($WP post create \
  --post_type=urban_team \
  --post_status=publish \
  --post_title="Marcus Rivera" \
  --post_content="Marcus oversees all business recruitment, retention, and support programmes for the district. He brings a background in commercial real estate and small business consulting, with a passion for building thriving main street economies." \
  --post_excerpt="Director of Business Development focused on recruitment and retention." \
  --porcelain)
$WP post meta update "$T2" _team_title    "Director of Business Development"
$WP post meta update "$T2" _team_email    "mrivera@urbandistrict.example.com"
$WP post meta update "$T2" _team_phone    "(206) 555-0101"
$WP post meta update "$T2" _team_linkedin "https://linkedin.com/in/marcusrivera-example"

success "2 sample team members created"

# ─── Sample blog posts (for News section) ─────────────────────────────────────
info "Creating sample news posts..."

$WP post create \
  --post_type=post \
  --post_status=publish \
  --post_title="District Announces \$2.4M Streetscape Grant" \
  --post_content="The Downtown Business Improvement District has secured a \$2.4 million state grant to fund the long-awaited Main Street Streetscape Improvement Project. Work is scheduled to begin in spring 2025, with completion expected by late 2026." \
  --post_excerpt="State grant secured for Main Street corridor improvements." \
  --porcelain > /dev/null

$WP post create \
  --post_type=post \
  --post_status=publish \
  --post_title="Summer Market Returns June 14 — Vendors Now Accepted" \
  --post_content="Applications are now open for the 8th Annual Downtown Summer Market taking place on June 14. Local makers, growers, and artisans are invited to apply for a booth at Central Plaza." \
  --post_excerpt="Vendor applications open for the 8th Annual Downtown Summer Market." \
  --porcelain > /dev/null

$WP post create \
  --post_type=post \
  --post_status=publish \
  --post_title="New Safety Ambassador Cohort Completes Training" \
  --post_content="Twelve new community ambassadors have completed their training and will join the existing team covering the downtown core seven days a week. The expanded program is funded through a combination of BID assessments and city support." \
  --post_excerpt="12 new ambassadors join the downtown safety program." \
  --porcelain > /dev/null

success "3 sample news posts created"

# ─── Customizer defaults ──────────────────────────────────────────────────────
info "Setting Customizer defaults..."
$WP option patch update theme_mods_urban-cms district_name       "Downtown Demo District"
$WP option patch update theme_mods_urban-cms district_tagline    "A vibrant urban destination"
$WP option patch update theme_mods_urban-cms district_phone      "(206) 555-0100"
$WP option patch update theme_mods_urban-cms district_email      "info@urbandistrict.example.com"
$WP option patch update theme_mods_urban-cms stat_1_number       "340+"
$WP option patch update theme_mods_urban-cms stat_1_label        "Member Businesses"
$WP option patch update theme_mods_urban-cms stat_2_number       "1.2M"
$WP option patch update theme_mods_urban-cms stat_2_label        "Annual Visitors"
$WP option patch update theme_mods_urban-cms stat_3_number       "18"
$WP option patch update theme_mods_urban-cms stat_3_label        "Active Initiatives"
$WP option patch update theme_mods_urban-cms stat_4_number       "\$48M"
$WP option patch update theme_mods_urban-cms stat_4_label        "Economic Impact"
$WP option patch update theme_mods_urban-cms district_map_lat    "47.6062"
$WP option patch update theme_mods_urban-cms district_map_lng    "-122.3321"
$WP option patch update theme_mods_urban-cms district_map_zoom   "15"
$WP option patch update theme_mods_urban-cms newsletter_enabled  "1"
success "Customizer defaults set"

# ─── Done ─────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo -e "${GREEN}  Urban CMS setup complete!${RESET}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo ""
echo "  Front-end   →  http://localhost:8080"
echo "  Admin       →  http://localhost:8080/wp-admin  (admin / admin)"
echo "  phpMyAdmin  →  http://localhost:8081"
echo ""
