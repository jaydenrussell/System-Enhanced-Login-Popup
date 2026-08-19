# System - Login Popup (Modern)

A Joomla 3.x system plugin that displays the login/logout form as a modal popup. Based on [ExtStore Login Popup v1.0.1](http://extstore.com) with an added **Modern layout** featuring customizable branding, full-page blur, and responsive design.

## Features

### Default Layout
The original ExtStore login popup style — unchanged and fully functional.

### Modern Layout
- **Club logo** inside the modal card (configurable size: small/medium/large)
- **Password visibility toggle** (show/hide)
- **Customizable title** (e.g., "Welcome Back")
- **Remember Me** options: show/hide, checked/unchecked
- **Full-page blur** (`backdrop-filter`) when modal opens
- **Unblur Header/Nav** — keep header/nav sharp while page blurs (Yes/No toggle with editable CSS selector)
- **Modal positioning** — top, center, bottom, or custom offset
- **Signup section** — customizable text and link
- **Forgot login** link always visible
- **Responsive** — works on mobile and desktop

### Plugin Parameters

| Parameter | Description |
|-----------|-------------|
| Popup Layout | Default or Modern |
| Club Logo | Logo image for Modern layout (media picker) |
| Logo Size | Small (60px), Medium (90px), Large (120px) |
| Modal Vertical Position | Top, Center, Bottom, Custom |
| Modal Top Offset | Pixels from top (for Custom position) |
| Unblur Header/Nav | Yes = keep header sharp, No = blur with page |
| Unblur CSS Selector | CSS selector for elements to keep sharp |
| Custom Title Enabled | Show/hide title above login form |
| Custom Title Text | Title text (default: "Welcome Back") |
| Remember Me Display | Show Checked, Show Unchecked, Hide Checked, Hide Unchecked |
| Signup Text | Text before signup link |
| Signup Link Text | Signup link text |
| Selector | CSS selector for popup trigger links |
| Login Redirect | Menu item for post-login redirect |
| Logout Redirect | Menu item for post-logout redirect |
| Show Greeting | Show/hide greeting text |
| Show Name/Username | Show name or username |
| Encrypt Login Form | Submit via SSL |

## Installation

1. Download `plg_system_loginpopup.zip`
2. Go to **Extensions > Manage > Install** in Joomla admin
3. Click **Upload Package File** and select the ZIP
4. Go to **Extensions > Plugins** and enable **System - Login Popup**
5. Configure parameters as needed

## Upgrade from v1.0.x

This plugin uses `method="upgrade"` in the manifest. Simply install the new ZIP over the existing plugin — your parameters will be preserved.

## License

GNU General Public License version 2 or later. See [LICENSE](LICENSE).

## Credits

- Original plugin: [ExtStore Login Popup v1.0.1](http://extstore.com) (2014)
- Modern layout and enhancements: Jayden Russell (2026)
