= Lemmy

Lemmy is an extension to the Mustache templating language that adds extra
features on top of Mustache.

This PHP implementation is based on bobthecow's Mustache.php implementation

== Why?

I made Lemmy to solve a pretty specific problem: Currently, a project I'm
working on has dozens and dozens of template files, written in PHP, and 
more and more are being written every day by the designers working on this
project.  What happens when we decide to update the program that renders
these templates to another language?  Yea, thats gonna be a problem.

So we decided to switch to Mustache, since there are implementations
in all of the languages we are considering using for the future 
rewrite (namely, Node.js, Ruby, Python), as well as the current 
system.  But Mustache doesn't quite support a hand-full of the 
features we are currently taking advantage of - not only that, 
but the features themselves are pretty non-Mustache in their
nature.

So, taking some inspiration from other templating languages 
(specifically Mako) I wrote Lemmy - the sideburns to Mustache. 

== What does Lemmy do?

Lemmy is Mustache. It just adds a handful of useful features.

=== Filters

While filters can be executed in Mustache currently, it feels a
little awkward, as its the data (model) that implements the functions
called in the templates.  I wanted it to be part of Lemmy. The syntax
for filters is similar in syntax to Mako:

    <?php
    require_once('lemmy.php');
    $l = new Lemmy();
    $tmpl = "I was born on {{birthday|d|%Y-%m-%d}}"
    $data = array('birthday', 'October 11 1985');
    $l->render($t, $data); // I was born on 1985-10-11

    $tmpl = "Welcome to <div id="{{name|c}}">{{name}}</div>"
    $data = array('name' => 'the thunderdome');
    $l->render($t, $data); // Welcome to <div id="the-thunderdome">the thunderdome</div>"

There's a bunch of filters built in, and you can easily add more

=== Conditions

