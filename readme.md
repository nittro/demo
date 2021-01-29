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

1. Register the Nittro extension in `config.neon`
2. Update flash messages rendering in templates to make use of the
   `n:flash` macro in order to normalize flash behaviour and appearance
3. Add form error rendering macros to templates
4. Add a default transition to the content snippet in `@layout.latte`
5. Disable transitions & history for some actions (like adding / removing a comment)
6. Replace `redirect()` with `postGet()` & `redrawControl()` where
   applicable to save roundtrips
7. Update site CSS to make everything beautiful


#### 3. Introduce ComponentEvents

1. Install the `jahudka/component-events` Composer package
2. Register the ComponentEvents extension in `config.neon`
3. Make the `CommentCount` component an event subscriber


#### 4. Replace `window.confirm()` with something nicer

1. Replace inline `window.confirm()` calls with the `data-prompt` attribute
2. Update stylesheet to make buttons in Nittro dialog look like Bootstrap buttons


#### 5. Use dynamic snippets to enhance comments

1. Update component code: send payload instead of rendering when a
   comment is deleted and render only new comment when one is added
2. Update template: add `n:dynamic`, specify dynamic element and
   create dynamic snippets out of list items; also change remove button
   behaviour to use client-side dynamic snippet removal
3. Add some animations when a comment is added or removed


#### 6. Try out the CheckList component

1. Add `_stack` initialisation code in `@layout.latte`
2. Initialise CheckList in post editor template


#### 7. Let's be modern and support drag & drop uploads

1. Initialise DropZone in post editor template
2. Update stylesheets


#### 8. ... but let's also be nice and clean up after ourselves

1. Wrap CheckList and DropZone initialisation in snippet setup callback
2. Add snippet teardown callback


#### 9. Let's get rid of the separate page for the Post form

1. Add the appropriate `n:dialog` macro attributes to links
   that should open in a dialog
2. Add the `data-action="cancel"` attribute to the cancel button of the
   form to improve behaviour when the form is open in a dialog
3. Update snippet redrawing behaviour
