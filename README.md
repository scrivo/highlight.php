highlight.php
=============

A port of highlight.js by Ivan Sagalaev to PHP

Javascript code highlighting is very convenient and in many cases just what
you want to use. Especially for programming blogs I would not advice you to
use otherwise. But there are occasions where you're better off with a more 
'static' approach, for instance if you want to send highlighted code in an 
email or for API documents. For this I needed a code highlighting program 
preferably written in PHP.

I could not found any satisfactory solutions so I decided to port one from
JavaScript. After some comparison based on license, technology, language
support highlight.js (softwaremaniacs.org/soft/highlight/en/) came out most
favorable in my opinion.

It was my decision not to make a PHP highlighter but to do a port of 
highlight.js, these are different things. The goal was to make it work
exactly as highlight.js so language definitions and CSS files of the 
original program could be used. 
