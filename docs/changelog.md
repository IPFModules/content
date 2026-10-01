# Content 2.0.0 beta
Release date : 01/10/2026
## Added
- Page builder based on GrapesJS (0.23.6, bundled in assets/grapesjs): a page can use the new *Building blocks* layout instead of the content body
- Building blocks can be nested in named slots and contain editable zones: rich text, plain text, image and link
- Building block types are IPF objects managed in the admin (template, CSS, icon, allowed nested blocks), default types are seeded on install and update
- The building blocks of a page are stored as IPF objects (building blocks and zones) and rendered on the server; the rendered HTML is cached on the page and used for display, search and teasers
- Images in the page builder are chosen and uploaded in the ImpressCMS image manager
- Cloning a page also clones its building blocks

# Content 1.4.0
Release date : 01/03/2022
- Update version information
- Version that is bundled with ImpressCMS 1.4.4

# Content 1.3.0
Release date : 24/12/2019
- Update version information
- Check if it works with PHP 7.3 (yes)

# Content 1.2.2
Release date: 10 Oct 2018
## Fixed
 * Module version number is now text, no longer number
 
# Content 1.2.1 Final
Release date: 30 Dec 2016
## Fixed
 * Missing delimiter in admin caused fatal error
 * #449 check for isNew before starting clone
 
# Content 1.2.0
Release date: 15 Sept 2013

## Fixed
* #12 edit Content > can not create a Symlink
* #246 false pagination
* #250 sometimes no function for comment
* #282 Clone of content also clones pageviews
* #409 bug with content pagination
* #434 Content Menu Block of Module "Content" generates links that wont show comment feature
