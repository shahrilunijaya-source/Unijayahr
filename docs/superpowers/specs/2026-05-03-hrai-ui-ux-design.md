# HRAI UI/UX Design — Holistic Redesign

**Date:** 2026-05-03  
**Goal:** Apply mylrmp design patterns + micro-interactions, responsive design, visual hierarchy, and user feedback mechanisms across entire app holistically.

**Scope:** 14 resources + 9 custom pages + dashboard  
**Approach:** All 4 UI/UX improvements simultaneously (not phased)

---

## Design Tokens & Color System

### Primary Palette
- **Brand Red:** `#CC0000` (primary actions, active states, highlights, brand accents)
- **Navy:** `#061B31` (headers, emphasis text, dark interactive elements)
- **Slate:** `#64748D` (secondary text, placeholders, muted UI)
- **Light Background:** `#F8FAFC` (page background, hover states, disabled backgrounds)
- **Border:** `#E5EDF5` (dividers, input borders, section separators)
- **White:** `#ffffff` (content areas, card backgrounds, input backgrounds)

### Typography
- **Font Family:** Poppins (globally applied)
- **H1 (Page Title):** Navy, 1.3125rem (21px), 700 weight, letter-spacing -0.035em
- **H2 (Section Title):** Navy, 0.875rem (14px), 600 weight, uppercase, letter-spacing 0.07em
- **H3 (Subsection):** Navy, 0.875rem (14px), 500 weight
- **Body Text:** Slate or Navy, 0.875rem (14px), 400 weight, line-height 1.5
- **Form Labels:** Navy, 0.8125rem (13px), 500 weight
- **Small Text / Captions:** Slate, 0.75rem (12px), 400 weight
- **Breadcrumbs:** Red (`#CC0000`), 0.8125rem, 500 weight, hover darker red (`#AA0000`)

### Spacing Scale
Consistent 4px base unit: 4px, 8px, 12px, 16px, 20px, 24px, 28px, 32px

- **Card padding:** 20px (content area)
- **Section gaps:** 20px–24px
- **Form field gaps:** 12px (vertical)
- **Table cell padding:** 12px (vertical) 16px (horizontal)
- **Page padding:** 24px–28px desktop, 20px tablet, 16px mobile

---

## Component Styling

### Buttons
- **Base:** 6px radius, 500 font weight, 0.875rem font size, Poppins
- **Primary Button:**
  - Background: Red (`#CC0000`)
  - Text: White
  - Hover: Scale 102%, shadow deepens, background darkens to `#AA0000`
  - Active/Press: Scale 98%, shadow removed (depressed effect)
  - Disabled: 50% opacity, pointer-events none, cursor not-allowed
  - Loading: Spinner icon inside, text hidden, disabled state
  - Transition: 0.12s ease on all properties
- **Secondary Button:**
  - Background: Light gray (`#F8FAFC`)
  - Border: 1px `#E5EDF5`
  - Text: Navy
  - Hover: Dark gray background, deeper border
- **Danger Button:**
  - Background: Red with opacity (rgba version)
  - Text: White
  - On hover: Darker red

### Form Inputs
- **Container (.fi-input-wrp):**
  - Border: 1px solid `#E5EDF5`
  - Radius: 8px
  - Background: White
  - Transition: border-color 0.12s, box-shadow 0.12s
- **Focus State:**
  - Border: Red (`#CC0000`)
  - Box-shadow: 0 0 0 3px rgba(204, 0, 0, 0.10) (red tinted shadow)
- **Error State:**
  - Border: Red (#dc2626)
  - Box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08)
  - Error message below input: Red text, 0.75rem, small warning icon
  - Real-time validation: Clears when valid
- **Disabled State:**
  - Background: Light gray (`#F8FAFC`)
  - Border: Light gray
  - Opacity: 0.7
  - Cursor: not-allowed
- **Placeholder Text:** Slate color, 0.875rem
- **Label (above input):**
  - Navy color, 0.8125rem, 500 weight
  - Margin below input: 4px
  - Display: Block (full width)

### Selects & Dropdowns
- **Same as input styling** (border, focus, error, disabled)
- **Appearance:** none (custom styling)
- **Arrow Icon:** Navy, right-aligned (6px padding from right edge), 1.5em size
- **Option Text:** Navy, 0.875rem

### Tables
- **Container:**
  - Radius: 10px
  - Border: 1px solid `#E5EDF5`
  - Overflow: hidden (for border-radius to apply)
  - Shadow: rgba(50, 50, 93, 0.10) 0 8px 24px -8px
- **Header Row:**
  - Background: Light gray (`#F8FAFC`)
  - Text: Uppercase small labels, slate, 0.6875rem, 700 weight, letter-spacing 0.07em
  - Border-bottom: 1px `#E5EDF5`
  - Padding: 9px 16px
- **Body Rows:**
  - Background: White
  - Text: Navy, 0.84375rem
  - Border-bottom: 1px `#F1F5F9` (lighter border)
  - Padding: 12px 16px
  - Last row: No bottom border
- **Row Hover:**
  - Background: `#fafbfc` (subtle pale shift)
  - Transition: 0.1s
  - No scroll or layout shift
- **Pagination:**
  - Active page: Red background, white text
  - Inactive: Gray, hover darker
  - Styling: 6px radius buttons

### Badges / Status Pills
- **Base:** Inline-flex, aligned center, gap 5px, 11.5px font size, 500 weight, 3–10px padding, 6px radius
- **Success Badge:**
  - Background: `#f0fdf4` (light green)
  - Text: `#15803d` (dark green)
  - Dot: Green (`#16a34a`)
- **Warning Badge:**
  - Background: `#fffbeb` (light amber)
  - Text: `#d97706` (amber)
  - Dot: Amber
- **Danger Badge:**
  - Background: `#fef2f2` (light red)
  - Text: `#dc2626` (red)
  - Dot: Red
- **Info Badge:**
  - Background: `#eff6ff` (light blue)
  - Text: `#2563eb` (blue)
  - Dot: Blue
- **Primary (Brand) Badge:**
  - Background: `#fff1f1` (light red)
  - Text: Red (`#CC0000`)
  - Dot: Red

### Cards / Sections
- **Container:**
  - Radius: 10px
  - Background: White
  - Border: 1px `#E5EDF5`
  - Shadow: rgba(50, 50, 93, 0.10) 0 8px 24px -8px, rgba(0, 0, 0, 0.06) 0 4px 12px -4px
- **Header (optional):**
  - Padding: 14px 20px
  - Border-bottom: 1px `#E5EDF5`
  - Heading: Navy, 0.875rem, 600 weight
- **Content:**
  - Padding: 20px
- **Hover (interactive cards):**
  - Shadow deepens
  - Scale: 101%
  - Transition: 0.15s ease

---

## Responsive Design

### Breakpoints
- **Mobile:** 320px–639px
- **Tablet:** 640px–1023px
- **Desktop:** 1024px+

### Mobile (320px–639px)
- **Layout:** Single column
- **Sidebar:** Hidden by default, hamburger toggle shows/hides as overlay modal
- **Tables:** Stacked card layout or horizontal scroll (show key columns only, others scroll)
- **Forms:** Full-width inputs, full-width buttons
- **Typography:** Reduce font sizes by 0.0625rem (micro-reduction for space)
- **Padding:** 16px page padding, 12px card padding
- **Navigation:** Bottom nav or collapsed sidebar
- **Images:** Responsive images, max-width 100%

### Tablet (640px–1023px)
- **Layout:** Two-column where applicable
- **Sidebar:** Visible but narrower (120px), icons only (show label on hover)
- **Tables:** 2–3 columns visible, horizontal scroll for overflow
- **Forms:** Two-column grid if space allows
- **Typography:** Default sizing
- **Padding:** 20px page padding

### Desktop (1024px+)
- **Layout:** Full multi-column layouts (2–3 col where sensible)
- **Sidebar:** Fixed 240px, always visible
- **Tables:** All columns visible (scroll if necessary)
- **Forms:** Natural layout, grouping
- **Typography:** Default sizing
- **Padding:** 24px–28px page padding

### Sidebar Responsiveness
- **Desktop:** 240px fixed width, always visible
- **Tablet:** 120px width, label-less icon buttons (label appears on hover), tooltips
- **Mobile:** Hidden, hamburger toggle shows/hides as full-height overlay modal with semi-transparent backdrop

### Mobile Navigation
- **Hamburger button:** Top-left, three horizontal lines, navy color
- **Toggle action:** Slide in sidebar from left (100% width overlay), semi-transparent black backdrop
- **Dismiss:** Tap backdrop or close icon, or tap a nav item

---

## Micro-Interactions & Animations

### Button Interactions
- **Hover:** Scale 102%, shadow deepens (transition 0.12s ease)
- **Active/Press:** Scale 98%, shadow removed (depressed effect), transition 0.12s ease
- **Focus:** Outline visible on keyboard navigation (optional subtle ring)
- **Loading:** Spinner icon replaces text/icon, disabled state, no hover effects
- **Ripple (optional):** Subtle radial fade from click point, 0.3s, very light (low opacity)

### Form Focus Animations
- **Input focus:**
  - Border color shift to red (0.12s ease)
  - Shadow glow appears (0.12s ease)
- **Label:** Optional color shift to navy (if not already)
- **Validation feedback:** Error message slides in/fades in (0.15s)

### Hover States (General)
- **Table rows:** Background shift to `#fafbfc`, 0.1s transition (smooth not jarring)
- **Cards:** Shadow deepens, scale 101%, 0.15s ease
- **Links:** Color shift to red, underline fade in, 0.1s
- **Sidebar nav items:** Red tint background (rgba(204, 0, 0, 0.18)), icon brightens, 0.1s

### Loading States
- **Spinner:** Navy color, 1s rotation, centered over content
- **Skeleton loaders:** Light gray shimmer (pulse 0.8→1.0 opacity, 1.5s loop)
- **Button loading:** Spinner inside button, text hidden, pointer disabled
- **Page transition:** Fade in from light gray tint (0.2s cubic-bezier)

### Transitions & Animations
- **Page navigation:** Fade in (0.2s) from semi-transparent overlay
- **Modals:** Scale up + fade in (0.3s cubic-bezier(0.16, 1, 0.3, 1))
- **Toast notifications:** Slide in from top (0.3s), auto-dismiss 4s, slide out on dismiss
- **Collapsible sections:** Height transition (0.2s ease)

### Disabled State Animations
- **No hover effects on disabled elements**
- **Opacity reduced 0.5**
- **Cursor: not-allowed**
- **Pointer-events: none**

---

## Visual Hierarchy & Information Scannability

### Page Structure
- **H1 (Page Title):**
  - Navy, 1.3125rem, 700 weight
  - Top margin: 0
  - Margin below: 8px
- **Breadcrumbs (above H1):**
  - Red links, 0.8125rem, 500 weight
  - Hover darker red
  - Separator between items
- **Page description (subheading):**
  - Slate color, 0.8125rem
  - Margin below: 12px

### Section Hierarchy
- **H2 (Section heading):** Navy, 0.875rem, 600 weight, uppercase, 0.07em letter-spacing
- **H3 (Subsection):** Navy, 0.875rem, 500 weight
- **Body text:** Slate or navy, 0.875rem, 1.5 line-height, max-width 70 chars per line (readability)

### Color Hierarchy
- **Red:** Primary actions, active states, critical alerts, brand
- **Navy:** Headers, emphasis, structure, important data
- **Slate:** Secondary text, descriptions, helper text, placeholders
- **Light gray:** Backgrounds, borders, disabled states, muted elements

### Whitespace Strategy
- **Card content padding:** 20px (breathing room)
- **Between sections:** 20px–24px (visual separation)
- **Form field gaps:** 12px vertical (not cramped)
- **Table rows:** 12px padding (scannable, not dense)
- **Typography line-height:** 1.5 (readable, not cramped)

### Information Scannability
- **Form labels:** Uppercase small, slate, distinct from content
- **Data tables:**
  - Header row visually distinct (light bg, navy uppercase labels)
  - Content rows clean white, easy to scan
  - Alternating row colors optional (skip for clean look)
- **Form layout:** Label above input (vertical stack), error below with red icon
- **Lists:** Bullet points or numbered, consistent 12px indentation
- **Callouts/alerts:** Colored left border (red for danger, amber for warning, green for success)
- **Icons:** Use consistently (checkmark for success, X for error, ! for warning)

### Data Display
- **Important data:** Navy, 500+ weight
- **Secondary data:** Slate, normal weight
- **Timestamps:** Slate, smaller font
- **Status badges:** Colored pills for quick recognition

---

## User Feedback Mechanisms

### Success Feedback
- **Toast notification:**
  - Background: `#f0fdf4` (light green)
  - Text: `#15803d` (dark green)
  - Icon: White checkmark
  - Position: Top-right corner
  - Auto-dismiss: 4 seconds
  - Animation: Slide in from top (0.3s), slide out on dismiss
- **Message examples:** "Saved successfully", "Leave request submitted", "Profile updated"

### Error Feedback
- **Toast notification:**
  - Background: `#fef2f2` (light red)
  - Text: `#dc2626` (red)
  - Icon: White X
  - Position: Top-right corner
  - Dismiss: Manual (stays until clicked/dismissed)
  - Animation: Slide in from top (0.3s)
- **Message examples:** "Email already registered", "Failed to save", "Required field"
- **Inline form error (below input):**
  - Red text, 0.75rem
  - Icon: Small warning icon
  - Appears on blur or form submission
  - Clears on valid input (real-time validation)

### Validation Feedback
- **Input focus:** Border turns red + glow shadow on error
- **Real-time validation:** Check field validity as user types, clear error if valid
- **Form submission:** Show all errors at once, focus on first error field
- **Success checkmark:** Optional green checkmark on valid field (subtle)

### Loading Feedback
- **Button loading:** Spinner replaces icon, text hidden, disabled state
- **Page/section load:**
  - Full-page skeleton loaders (light gray pulse, 1.5s loop)
  - Rows load with staggered delay (0.05s per row)
  - "Loading..." text optional (skeleton preferred)
- **Table data load:** Show empty header, skeleton rows appear progressively

### Confirmation Dialogs
- **Modal overlay:** Semi-transparent dark (rgba(0, 0, 0, 0.5)), covers entire page
- **Card center:**
  - White background, 10px radius, shadow
  - Navy header text, description body
  - Two buttons: "Cancel" (gray) and "Confirm" (red)
  - Icon: Optional warning/question icon
- **Animation:** Scale + fade in (0.3s), scale out on dismiss
- **Message examples:** "Delete permanently?", "Submit for approval?", "Discard changes?"

### Empty States
- **Centered card with:**
  - Icon (navy or gray, 48px)
  - Heading (navy, 0.875rem)
  - Description (slate, 0.8125rem)
  - CTA button (red primary button)
- **Message examples:** "No leaves found. Request a leave.", "No records yet. Create one to get started."

### Tooltips
- **Appearance:** Dark navy background, white text, 0.75rem, 8px padding
- **Trigger:** Hover on icon or disabled element
- **Animation:** Fade in (0.15s)
- **Position:** Above or below with arrow indicator (auto-adjust if edge collision)
- **Message examples:** "Required field", "Click to expand", "Hover for details"

### Progress Indicators
- **Stepper (multi-step forms):**
  - Current step: Red background, white number
  - Completed: Green background, white checkmark
  - Pending: Light gray, navy number
- **Progress bar:** Red foreground, light gray background, 100% width container
- **Label:** Step count "Step 2 of 4" below steps

---

## Scope — Pages & Resources

### Resources (14 total)
All receive holistic redesign (responsive, micro-interactions, visual hierarchy, user feedback):

1. **UserResource** — User management, listing, creation, editing
2. **EmployeeProfileResource** — Employee profile data, view/edit
3. **LeaveRequestResource** — Request listing, creation, approval workflow
4. **LeaveBalanceResource** — Balance view, filtered by user, carry-over display
5. **LeaveTypeResource** — Admin: manage leave types, configure rules
6. **AttendanceResource** — Attendance records, clock in/out, bulk actions
7. **KpiPeriodResource** — KPI periods, staff assignment, review cycles
8. **KpiRubricTemplateResource** — Rubric templates for KPI scoring
9. **KpiReviewResource** — Review entries, scoring interface, feedback
10. **PersonalityAssessmentResource** — Assessment admin, configuration
11. **PersonalityResultResource** — Results view, filtering, export
12. **StaffDirectoryResource** — Staff listing, search, filtering by department/unit
13. **HandbookPartResource** — Handbook sections, admin editing, publishing
14. **StaffSuggestionResource** — Suggestions/ideas tracking, moderation queue

### Custom Pages (9 total)
1. **Dashboard** — Overview widgets, key metrics, announcements, quick actions
2. **MyProfile** — User profile view/edit, avatar, contact info, password change
3. **MyLeavePage** — Personal leave requests, balance summary, request form
4. **MyAttendancePage** — Personal attendance history, clock in/out, monthly summary
5. **OrgChart** — Organizational hierarchy visualization, department structure
6. **Handbook** — Browse company handbook, search, categories
7. **PersonalityTest** — Take personality assessment, progress, submit
8. **MySuggestions** — User suggestions/ideas, view own submissions, status
9. **LeaveApprovalsPage** — Manager approvals, pending requests, approve/reject with notes
10. **TeamAttendancePage** — Manager: team attendance view, attendance summary, patterns

### Default Page
- **Dashboard** — Default landing page after login, shows overview and quick actions

### All Pages & Resources Receive
- ✅ Responsive layouts (mobile/tablet/desktop)
- ✅ Micro-interactions (hover, focus, active, loading states)
- ✅ User feedback (toasts, validation errors, confirmation dialogs, loading spinners)
- ✅ Visual hierarchy (clear typography, whitespace, color coding)
- ✅ mylrmp design patterns (color palette, component styling, spacing)

---

## Implementation Notes

### CSS Architecture
- Tailwind v3 (no v4 features)
- Custom CSS in `resources/css/filament/app/theme.css`
- Vite build: `npm run build` for production

### Component Customization
- Filament v3 uses `.fi-*` classes (override in theme.css)
- Use `!important` sparingly (only when Filament defaults conflict)

### Animations
- Use CSS `transition` property (0.1s–0.3s depending on element)
- Use `cubic-bezier` for natural easing
- Keep durations under 0.5s for snappy feel

### Accessibility
- Keyboard navigation: All interactive elements focusable
- Color contrast: WCAG AA (4.5:1 minimum for text)
- Labels: Every form input has associated label
- Semantic HTML: Use `<button>`, `<input>`, `<label>` correctly

---

## Success Criteria

✅ All 14 resources styled consistently  
✅ All 9 custom pages + dashboard responsive  
✅ Micro-interactions applied to buttons, forms, tables, cards  
✅ Loading states, success/error toasts, validation feedback working  
✅ Mobile/tablet/desktop layouts tested and validated  
✅ Visual hierarchy clear on all pages  
✅ Color palette applied consistently (red for primary, navy for headers, slate for secondary)  
✅ Spacing consistent (4px base unit grid)  
✅ Typography hierarchy applied (H1, H2, H3, body, labels, small text)  

---

## Future Enhancements
- Dark mode toggle (optional, beyond scope)
- Advanced data visualizations (charts, graphs)
- Print-friendly styles
- PDF export styling
