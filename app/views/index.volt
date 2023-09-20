<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon"/>
    {{ tag.title() }}
    {{ cssBefore is not empty ? cssBefore : null }}
    {{ stylesheet_link('/css/all.min.css') }}
    {{ stylesheet_link('/css/adminlte.min.css') }}
    {{ css is not empty ? css : null }}
</head>

{{ content() }}

{{ javascript_include('/js/jquery-3.7.1.min.js', false) }}
{{ javascript_include('/js/bootstrap.bundle.min.js', false) }}
{{ javascript_include('/js/adminlte.min.js', false) }}
{{ js is not empty ? js : null }}

</html>
