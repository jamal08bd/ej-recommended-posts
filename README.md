# README #

### What is this plugin for? ###

Recommended Posts Block - This Gutenberg block plugin displays a grid of user-selected articles (Posts)

![frontend-view.png](https://github.com/jamal08bd/ej-recommended-posts/blob/main/src/doc/frontend-view.png)

### Technical details and prerequisite ###

- Author: AFM Jamal Uddin
- Contributor: UI/UX Design by [Evermade](https://www.evermade.fi/)
- Tags: block
- Tested up to: WordPress 6.1.1
- Version: 0.1.0
- Requires PHP: 7.0
- Requires WordPress: 6.1
- License: GPL-2.0-or-later
- [License URI](https://www.gnu.org/licenses/gpl-2.0.html)

### Installation ###

* Plugin installation
	- Unzip and upload the entire `ej-recommended-posts` folder to the `/wp-content/plugins/` directory.
	- Activate the plugin through the **Plugins** screen (**Plugins > Installed Plugins**).

* Block setup
	- In the **posts/pages** Gutenberg editor, you can find this block (Recommended Posts Block) under the 'widgets' category.
	- You can also find this block by searching with the keyword 'recommended', or 'posts'.
	- After assigning the block to the editor, nothing will display, but you will see a setting panel in the right sidebar.
	- Setting panel has two options field - **TYPE AND SELECT POSTS** and **Display featured image**.
	- Typing to the field - 'TYPE AND SELECT POSTS' will suggest posts to select. Don't forget to select some posts :)
	- Toggle field - 'Display featured image' display/hide the featured image.

![backend-view.png](https://github.com/jamal08bd/ej-recommended-posts/blob/main/src/doc/backend-view.png)

### Screenshots ###
	- src/doc/frontend-view.png
	- src/doc/backend-view.png 

[Front end view](https://github.com/jamal08bd/ej-recommended-posts/blob/main/src/doc/frontend-view.png)

[Back  end view](https://github.com/jamal08bd/ej-recommended-posts/blob/main/src/doc/backend-view.png)

### Docs and support  ###

It is simple to install/configure and doesn't require special docs than this README. However, if you don't find the answers, you can reach out to this [email](mailto:jamal08.bd@gmail.com).

### Privacy notices ###

With the default configuration, this plugin, in itself, does not:

* track users by actions.
* write any personal data to the database.
* send/receive any data to/from external servers.
* or use cookies.

### Translations ###
 
The front-end view of this plugin is translation-ready. You can use [poedit](https://poedit.net/)  or [loco translate](https://wordpress.org/plugins/loco-translate/) to translate the frontend strings to your own language. Within the next few updates, the Editor view of this plugin will also become translatable.  

### For Developers ###
 
You are free to play with the code and develop your own Gutenberg plugin on top of this. Attribution is not mandatory but appreciated :)

* Summary of set up
	- Clone this repository and then open your favorite git terminal "ex: visual studio code, git bash, etc".
	- Run `npm install`. It will install all packages that are present on the repo's `package.json`.

* Dependencies
	- `package.json` has all dependencies and also has information about `engines`, meaning which `node` and `npm` version this application runs on.

* How to run tests
	- You can run several commands inside your plugin directory:

		- `$ npm start` - Starts the build for development.

		- `$ npm run build` - Builds the code for production.

		- `$ npm run format` - Formats files.

		- `$ npm run lint:css` - Lints CSS files.

		- `$ npm run lint:js` - Lints JavaScript files.

		- `$ npm run plugin-zip` - Creates a zip file for a WordPress plugin.

		- `$ npm run packages-update` - Updates WordPress packages to the latest version.

	- To enter the directory run:

		- `$ cd ej-recommended-posts`

  - And then you can open the `src` directory and play with files
  - Finally, run the above commands to build your changes and test your changes to the WordPress environment

### Contribution guidelines ###

* details coming...

### Who do I talk to? ###

* Repo owner, admin
	- You can contact the Author [Jamal Uddin](mailto:jamal08.bd@gmail.com).
* Other community or team contact
	- none

### Changelog ###
 
*= 0.1.0 =*
	
- initial release
