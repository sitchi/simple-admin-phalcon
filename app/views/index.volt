<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="/img/favicon.ico" type="image/x-icon"/>
    {{ renderTitle() }}
    {{ cssBefore is not empty ? cssBefore : null }}
    {{ stylesheet_link('/css/all.min.css') }}
    {{ stylesheet_link('/css/adminlte.min.css') }}
    {{ css is not empty ? css : null }}
</head>

{{ content() }}

{{ javascript_include('/js/jquery-3.4.1.min.js', false) }}
{{ javascript_include('/js/bootstrap.bundle.min.js', false) }}
{{ javascript_include('/js/adminlte.min.js', false) }}
{{ js is not empty ? js : null }}

</html>
