=== RoleCraft: User Access Control – Roles, Capabilities & Content Restriction ===
Contributors: angelorocha
Donate link: https://www.paypal.com/donate?hosted_button_id=DRE7DA2LZBA3U
Tags: user roles, capabilities, access control, content restriction, admin menu
Requires at least: 6.1
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.2.3
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Take control of WordPress with custom user roles, capabilities, admin menu access, widget control, and content restrictions in one plugin.

== Description ==

**RoleCraft User Access Control** is a lightweight and comprehensive access control solution for WordPress that gives administrators precise control over **what users can do, what they can access, and what content they can see**.

Manage custom user roles and capabilities, assign multiple roles to users, control dashboard menus and the admin bar, restrict widgets and sidebars, and protect content across posts, pages, and custom post types — without relying on multiple plugins or custom code.

Designed for **site administrators, developers, agencies, client websites, membership sites, LMS platforms, and custom WordPress projects**, RoleCraft User Access Control brings commonly scattered access-control features together in one clean and intuitive interface.

### 🔐 Roles & Capability Management

Create and manage the permission structure of your WordPress installation without editing code.

* **Custom Role Management:** Create, edit, and delete custom user roles directly from the WordPress dashboard.
* **Granular Capability Management:** Add or remove individual capabilities from custom roles for precise permission control.
* **Multiple Roles per User:** Assign multiple roles to the same user from their profile.
* **Bulk Role Assignment:** Add or remove roles from multiple users at once.
* **Fallback Role:** Automatically assign a configured fallback role when users are affected by the deletion of a role.
* **Automatic Role Assignment:** Automatically assign one or more selected roles to newly registered users.

### 🛡️ Dashboard & Admin Access Control

Control what different user roles can see and access inside the WordPress administration area.

* **Admin Menu Permissions:** Control the visibility and access of dashboard menus and submenus by user role.
* **Admin Bar Visibility:** Show or hide the WordPress admin bar for specific user roles.

This allows you to create cleaner, more focused dashboard experiences for clients, editors, contributors, members, and other user groups.

### 🧩 Widget & Sidebar Permissions

Control access to classic WordPress widgets and sidebars according to user roles.

* **Widget Access Control:** Restrict widget visibility and editing permissions based on user roles.
* **Sidebar Permission Manager:** Manage role-based permissions for specific sidebar widgets across the frontend and backend.

### 🔒 Content Restriction

Restrict access to content based on user roles without requiring custom development.

* **Custom Post Type Restrictions:** Choose which post types can use content restrictions.
* **Per-Post Access Control:** Restrict individual posts and pages by selecting which user roles are allowed to view their content.
* **Custom Restriction Messages:** Configure personalized messages displayed when access to restricted content is denied.

### ⚡ One Plugin, Multiple Access-Control Layers

Instead of installing several separate plugins to manage roles, capabilities, dashboard access, widgets, and restricted content, RoleCraft User Access Control brings these controls together in one focused solution.

From **user permissions and administrative access** to **frontend content restrictions**, everything can be managed from a unified interface.

### 🚀 Built for Modern WordPress

* **Lightweight by Design:** Built around WordPress Core APIs without unnecessary dependencies or feature bloat.
* **Modern PHP:** Requires PHP 8.1 or newer.
* **Developer Friendly:** Designed to fit client websites, custom WordPress projects, membership sites, LMS platforms, and other advanced WordPress installations.
* **No Coding Required:** Configure roles, capabilities, access rules, and restrictions directly from the WordPress dashboard.

### 💡 Common Use Cases

RoleCraft User Access Control is useful for:

* **Client Websites:** Give clients access only to the tools and dashboard areas they need.
* **Membership Websites:** Control access to content and functionality according to user roles.
* **LMS Platforms:** Separate administrative, instructor, and learner permissions.
* **Client Portals:** Create focused dashboard experiences with role-specific access.
* **Editorial Workflows:** Define precisely what different users and roles can manage.
* **Custom WordPress Applications:** Build granular permission structures without developing a custom access-control system from scratch.

== Installation ==

1. Install the plugin directly from **Plugins > Add New** in your WordPress dashboard.
2. Search for `RoleCraft User Access Control`.
3. Click **Install Now** and then **Activate**.
4. Navigate to the **RoleCraft User Access Control** menu in your WordPress dashboard.

== Frequently Asked Questions ==

= Can I assign multiple roles to the same user? =

Yes. You can assign multiple roles to an individual user directly from their WordPress profile and also apply role changes to multiple users in bulk.

= Can I create custom user roles? =

Yes. You can create, edit, and delete custom roles directly from the plugin interface without modifying code.

= Can I control individual capabilities? =

Yes. The capability editor allows you to add or remove individual capabilities from custom roles for more granular permission management.

= What happens when I delete a role that users are assigned to? =

You can configure a fallback role that is automatically applied when deleting a role would otherwise leave affected users without the expected role assignment.

= Can new users be assigned roles automatically? =

Yes. You can configure one or more roles to be automatically assigned to newly registered users.

= Can I control which dashboard menus users can access? =

Yes. You can control access to WordPress admin menus and submenus based on user roles.

= Can I hide the WordPress admin bar for specific roles? =

Yes. Admin bar visibility can be controlled according to user roles.

= Can I restrict widgets and sidebars by user role? =

Yes. You can control widget visibility and editing permissions and manage role-based access to specific classic sidebar widgets.

= Can I restrict content on custom post types? =

Yes. You can select which post types support content restrictions and then control access to individual posts based on user roles.

= Can I restrict individual posts or pages? =

Yes. Once restrictions are enabled for a post type, individual posts and pages can be configured with role-based content access rules.

= Can I customize the message shown when content is restricted? =

Yes. You can configure custom restriction messages using the built-in WYSIWYG editor.

= Do I need coding skills to use the plugin? =

No. The plugin provides a graphical interface for managing roles, capabilities, dashboard access, widgets, sidebars, and content restrictions.

== Screenshots ==

1. Roles List: manage custom user roles, see how many users are assigned to each, and add or remove roles.
2. Menu Items: control which WordPress admin menus are visible to each user role.
3. Capabilities List: add or remove individual capabilities for a selected role.
4. User Management: search for a user and view or edit the roles assigned to them.
5. Admin/Front Widgets: control classic widget visibility per user role, in the dashboard and on the frontend.
6. Sidebar Widgets: control individual block widget visibility per user role.
7. Settings: configure the default role, entries per screen, roles for new users, and content access control.
8. Content access control on the post/page edit screen, choosing which roles can view the content.
9. The "Permissions" row action added to the native WordPress Users list screen.
10. The role-assignment popup opened from the "Permissions" link on the Users list screen.

== Changelog ==
= 1.2.3 =
* Fix missing composer.json file

= 1.2.2 =
* Add phpcs, phpbf and phpstan;
* Update composer.json deps versions;
* Improve code quality for all plugin files;

= 1.2.1 =
* Removed unnecessary unauthenticated AJAX action registrations and replaced a role-based capability check with a proper WordPress capability.
* Applied a full PHPCS/WordPress Coding Standards cleanup and added missing code documentation.
* Fixed untranslated strings and refreshed the translation template file.
* Tested up to WordPress 7.1.

= 1.2.0 =
* Added option to manage user roles from the WordPress user list screen.
* Fix some plugin class and methods.

= 1.1.3 =

* Tested on WordPress 7+.
* Fixed default roles filter.
* Fixed warnings on sidebar widget permissions.
* Fixed pt_BR translation string issues.
* Fixed content access permissions when content has restrictions and the post type is unset in configuration.
* Removed deprecated `wp_get_sidebars_widgets` function from core.

= 1.1.1 =

* Added WYSIWYG editor for custom content access restriction messages.

= 1.1.0 =

* Frontend improvements and stability fixes.
* Added granular content access permissions by user roles.

= 1.0.0 =

* Initial public release.
