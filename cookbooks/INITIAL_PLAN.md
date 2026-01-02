# Stillward — Product Spec

Personal app to track new year's resolutions.

**Stack:** Laravel + Vue + Inertia. PWA for mobile access.

**Auth:** Laravel Socialite with Google (only login method).

---

## Concept

A simple app with three main areas:

1. **Goals Management** — define what you want to achieve
2. **Dashboard** — overview of your progress
3. **Quick Log** — fast daily tracking

Navigation between areas via bottom tab bar or sidebar.

---

## Categories

Goals are grouped into 4 fixed categories:

- **Body & Mind** — physical and mental health
- **Money & Work** — finances and career
- **Growth** — learning, habits, personal projects
- **Life** — relationships, leisure, experiences

Each category has a distinct color and icon for quick visual identification.

---

## Goal Types

Not all goals are measured the same way. Each type has its own tracking logic and Quick Log actions.

### Counter
Add or subtract units towards a numeric target.

- **Examples:** "Read 20 books", "Run 500 km", "Watch 50 movies"
- **Data:** current value, target value, unit of measure
- **Progress:** current value / target value
- **Quick Log actions:** +1 / -1 buttons, input for custom value (+5, +10...)

### Yes/No
Binary goal — achieved once or not.

- **Examples:** "Get AWS certification", "Visit Japan", "Buy new motorcycle"
- **Data:** completed (true/false), completion date
- **Progress:** 0% or 100%
- **Quick Log actions:** toggle on/off to mark as achieved

### Percentage
Manual progress towards completing something.

- **Examples:** "Finish Rust course", "Read Don Quixote", "Renovate house"
- **Data:** current percentage (0-100)
- **Progress:** the value entered directly
- **Quick Log actions:** slider or numeric input to update percentage

### Money
Monetary amount to reach.

- **Examples:** "Save €18,000", "Invest €10,000 in ETFs", "Emergency fund €6,000"
- **Data:** current amount, target amount, currency
- **Progress:** current amount / target amount
- **Quick Log actions:** input to add/subtract amount (+€500, -€200...)

---

## Area 1: Goals Management

Where I create, edit and organize my goals.

**Required features:**
- Create a goal with category, type, title and target
- Add optional description for context
- Define unit of measure when applicable (books, km, €...)
- Edit existing goals
- Archive goals I no longer care about without losing history
- View all goals grouped by category

---

## Area 2: Dashboard

Panoramic view of progress.

### Year Progress Bar

At the top, a horizontal bar showing how much of the year has passed. Visual reference to know if I'm on track. Example: if it's April and an annual goal is at 25%, I'm right on pace.

### Category Summary

Cards for each of the 4 categories showing:
- Number of active goals
- Average progress of those goals (%)
- Completed vs in-progress goals
- Mini sparkline or progress bar

### Global Chart

A line chart showing the evolution of total aggregated progress throughout the year. X-axis: time (months or weeks). Y-axis: global progress percentage.

### Pace vs Year Chart

Visual comparison between:
- Actual progress line (how I'm doing)
- Expected progress line (if it were linear throughout the year)

At a glance I can see if I'm ahead, on track, or behind relative to elapsed time.

### Per-Goal Charts

When viewing a goal's detail, individual chart showing:
- **Counter/Money:** timeline of accumulated value + target line
- **Percentage:** timeline of % + expected pace reference
- **Yes/No:** no chart, just status and completion date if applicable

### Recent Activity

List of latest Quick Log entries: which goal, what value, when. Quick context of what I've been doing.

---

## Area 3: Quick Log

The heart of daily use. Must be fast and frictionless.

**User flow:**
1. See the 4 categories as cards or buttons
2. Tap on a category
3. Active goals for that category are shown
4. Tap on a goal and execute action based on its type

**Actions by goal type:**
- **Counter:** +1 / -1 / custom value
- **Yes/No:** toggle completed
- **Percentage:** slider or input 0-100
- **Money:** input +/- amount

**Nice to have:**
- Optional short note when logging
- Immediate visual feedback on log

---

## Future: Streaks

Interesting concept but not yet defined. Options to consider:

- **Streak as optional property of any goal** — consecutive days logging something for that goal
- **Global app streak** — consecutive days using Quick Log (general gamification)
- **"Daily habit" type** — goal without numeric target, just yes/no each day, streak as derived data
- **Combine** — global streak + optional per-goal streaks
