# Student Learning Platform - LearnHub

## Overview
A modern, gamified PHP + SQLite learning platform for students with quiz features, leaderboards, badges, and peer challenges.

## Recent Changes (Oct 25, 2025)
- **UI Redesign**: Complete migration from Tailwind CSS to Bootstrap 5
- **Color Palette**: Custom theme with #1b4a5a (Primary), #feae55 (Accent), #ffeede (Light), #fafafa (Background)
- **Mobile-First**: Responsive design optimized for mobile devices
- **Challenge Arena**: New feature for students to compete in quiz battles
- **Database**: Migrated from MySQL to SQLite for Replit compatibility

## Project Architecture

### Tech Stack
- **Backend**: PHP 8.2
- **Database**: SQLite (with mysqli-compatible wrapper)
- **Frontend**: Bootstrap 5.3.2 + Bootstrap Icons
- **Fonts**: Poppins (Google Fonts)

### Key Pages
1. **index.php** - Dashboard with stats, leaderboard preview, and quick actions
2. **subject.php** - Subject listing with progress tracking
3. **chapters.php** - Chapter selection within a subject
4. **quiz_simple.php** - Modern quiz interface with timer and one-question-at-a-time
5. **leaderboard.php** - Student rankings with podium display
6. **challenge.php** - Challenge Arena for creating/joining quiz battles
7. **profile.php** - User profile and settings

### Database Schema
- **students**: User accounts
- **student_progress**: XP, levels, streaks
- **subjects**: Learning subjects (Math, Science, etc.)
- **chapters**: Subject chapters
- **summaries**: Chapter summaries/lectures
- **questions**: Quiz questions (multiple choice, true/false, direct)
- **question_options**: Answer choices
- **question_answers**: Correct answers
- **student_answers**: Student quiz responses
- **student_badges**: Achievement badges
- **challenges**: Challenge Arena matches
- **challenge_participants**: Challenge participants and scores

### Design System
- **Primary Color**: #1b4a5a (Deep Teal) - Main brand color
- **Accent Color**: #feae55 (Golden) - Highlights, CTAs
- **Light Color**: #ffeede (Cream) - Backgrounds, cards
- **Background**: #fafafa (Off White) - Page background

### Custom Components
- `stat-card`: Statistics display cards
- `leaderboard-item`: Ranking list items
- `subject-card`: Subject selection cards
- `challenge-card`: Challenge arena cards
- `question-card`: Quiz question container
- `bottom-nav`: Mobile navigation bar

## User Preferences
- Mobile-first design approach
- EA-style challenge invites with energetic, motivational UI
- Clean card layouts with rounded corners and shadows
- Smooth transitions and interactive feedback
- Touch-optimized buttons and controls
- Professional learning game aesthetic

## Key Features
1. ✅ **Dashboard**: Stats overview, subject progress, leaderboard preview
2. ✅ **Subject System**: Browse subjects, chapters, and lessons
3. ✅ **Quiz System**: Interactive quizzes with timer and scoring
4. ✅ **Leaderboard**: Competitive rankings with podium display
5. ✅ **Challenge Arena**: Create and join quiz battles with friends
6. ✅ **Progress Tracking**: XP, levels, streaks, badges
7. ✅ **Mobile Navigation**: Bottom nav bar for easy access

## Development Setup
1. PHP 8.2 installed via Replit modules
2. SQLite database auto-initialized on first run
3. Demo data automatically seeded
4. Auto-login enabled for development (user_id = 1)
   - **To disable for production**: Remove the auto-login block in `sign_in.php` (lines 4-10)
5. Server runs on port 5000

## Important Notes for Production
- **Disable Auto-Login**: Edit `sign_in.php` and remove the development auto-login code
- **Database**: The SQLite wrapper provides full mysqli compatibility for all existing code
- **Testing**: Recommended to spot-check Challenge Arena create/join flows before production use

## File Structure
```
/
├── assets/
│   └── custom.css          # Bootstrap 5 custom theme
├── inc/
│   ├── conn.php            # Database connection (uses SQLite wrapper)
│   ├── conn_sqlite.php     # SQLite implementation with mysqli compatibility
│   └── footer.php          # Footer with bottom navigation
├── index.php               # Dashboard
├── subject.php             # Subject listing
├── chapters.php            # Chapter listing
├── quiz_simple.php         # Modern quiz interface
├── leaderboard.php         # Leaderboard rankings
├── challenge.php           # Challenge Arena
├── profile.php             # User profile
├── sign_in.php             # Sign-in page (auto-login in dev)
└── head.php                # HTML head with Bootstrap includes
```

## Workflow
- **PHP Server**: Serves application on 0.0.0.0:5000

## Notes
- All existing PHP logic preserved from original codebase
- Only UI/UX enhanced with Bootstrap 5
- Database abstraction layer maintains compatibility with mysqli code
- Ready for mobile deployment
