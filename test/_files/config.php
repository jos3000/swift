<?php

return array(
    # use alternate debug resources
    'debug_use_alt_resources' => true,

    # use alternate debug resources
    'debug_use_alt_scripts' => true,

    # turn minification for CSS off
    'debug_minify_css_off' => true,

    # turn minification for JavaScript off
    'debug_minify_js_off' => true,

    # turn combine for CSS off
    'debug_combine_css_off' => true,

    # turn combine for JavaScript off
    'debug_combine_js_off' => true,

    # look for CSS and JavaScript files in the include path
    'use_include_path' => true,

    # where the Swift serves it's files via http
    'swift_url' => 'http://www.example.com/swift/',

    # where Swift and Minify can store the files they generate
    'inline_cache_dir' => '/home/caches/swiftcache/',

    # A list of the modules
    'modules' => array(
        'yui2-yahoo' => array(
            'path' => 'yui2.7.0/build/yahoo/yahoo-min.js',
            'debug_path' => 'yui2.7.0/build/yahoo/yahoo.js',
            'requires' => array()
        ),

        'yui2-dom' => array(
            'path' => 'yui2.7.0/build/dom/dom-min.js',
            'debug_path' => 'yui2.7.0/build/dom/dom.js',

            # list of other modules required by this one
            'requires' => array('yui2-yahoo')
        ),

        'page-aboutanim' => array(
            'serve_inline' => true,
            'path' => 'javascript/page-aboutanim.js',
            'requires' => array('yui-anim')
        ),

        'page-productzoom' => array(
            'source_in_template' => true,
            'serve_inline' => false,
            'requires' => array('yui-anim')
        ),
    )
);