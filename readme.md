Nittro Demo
===========

This is a simple blog based on the Nette WebProject template
which aims to demonstrate various Nittro features.

The project starts as a plain and simple Nette web application
and then each commit in the repository enhances the experience
a little bit, explaining what is being done and why.

To install the demo locally, simply clone the repository
and start the **PHP built-in webserver** from the `/public`
directory. (While you _could_ run the demo under Apache, there's
a lot of permission issues you could run into, so it's not
recommended.) Checkout the first commit, take a look around
to make sure you understand what's going on in the base
app, and then checkout the other commits one by one
to see what's being done. If you're using PhpStorm or
some other IDE which can show you side-by-side diffs
of the files updated in any given commit, I very much
recommend using that.

### Steps described by the commits

#### 1. Basic Nittro installation

1. Download a custom Nittro build
2. Link Nittro in `@layout.latte`
3. Install the `nittro/nette-bridges` Composer package
4. Make `BasePresenter` extend `Nittro\Bridges\NittroUI\Presenter`
   instead of `Nette\Application\UI\Presenter`
5. Setup default snippets in base presenter's `startup()` method


#### 2. Making things play nicer together

1. Register the Nittro Latte macros in `config.neon`
2. Update flash messages rendering in templates to make use of the
   `n:flash` macro in order to normalize flash behaviour and appearance
3. Add form error rendering macros to templates
4. Add a default transition to the content snippet in `@layout.latte`
5. Disable transition for some actions (like adding / removing a comment)
6. Replace `redirect()` with `postGet()` & `redrawControl()` where
   applicable to save roundtrips
7. Update site CSS to make everything beautiful
