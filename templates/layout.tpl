<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title|default:'Blog'}</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="site-header__brand" href="/">Blogy.</a>
    </div>
</header>

<main class="container site-main">
    {block name="content"}{/block}
</main>

<footer class="site-footer">
    <div class="container">
        <small>Copyright &copy;2025. All Rights Reserved.</small>
    </div>
</footer>
</body>
</html>
