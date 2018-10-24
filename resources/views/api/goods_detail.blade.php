<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <LINK rel="Bookmark" href="/favicon.ico" >
    <LINK rel="Shortcut Icon" href="/favicon.ico" />
    <link href="/css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <link href="/skin/default/skin.css" rel="stylesheet" type="text/css" id="skin1" />
    <link href="/lib/Hui-iconfont/1.0.1/iconfont.css" rel="stylesheet" type="text/css" />
    <link href="/css/style.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .custom-richtext {
            overflow: hidden;
            box-sizing: border-box;
            margin-top: 10px;
            padding: 0 10px;
            color: #333;
            text-align: left;
            word-wrap: break-word;
            font-size: 16px;
            line-height: 1.5;
        }

        .custom-richtext img+br {
            display: block;
            padding: 4px 0;
            content: ' ';
        }

        .custom-richtext p {
            margin:0;
        }

        .custom-richtext a {
            color: #07d;
        }

        .custom-richtext img {
            width: auto !important;
            height: auto !important;
            max-width: 100% !important;
            min-height: 1px;
            background: none;
            vertical-align: middle;
        }

        .custom-richtext ul,.custom-richtext ol {
            padding-left: 0;
            list-style-position: inside;
        }

        .custom-richtext blockquote {
            margin: 0 0 18px;
            padding: 0 0 0 15px;
            border-left: 5px solid #EEE;
        }

        .custom-richtext em {
            font-style: italic;
        }

        .custom-richtext strong {
            font-weight: bold;
        }

        .custom-richtext .selectTdClass {
            background-color: #edf5fa !important;
        }

        .custom-richtext table.noBorderTable td,.custom-richtext table.noBorderTable th,.custom-richtext table.noBorderTable caption {
            border: 1px dashed #ddd !important;
        }

        .custom-richtext table {
            display: table;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .custom-richtext td,.custom-richtext th {
            padding: 5px 10px;
            border: 1px solid #DDD;
        }

        .custom-richtext caption {
            padding: 3px;
            border: 1px dashed #DDD;
            border-bottom: 0;
            text-align: center;
        }

        .custom-richtext th {
            border-top: 2px solid #BBB;
            background: #F7F7F7;
        }

        .custom-richtext .ue-table-interlace-color-single {
            background-color: #fcfcfc;
        }

        .custom-richtext .ue-table-interlace-color-double {
            background-color: #f7faff;
        }

        .custom-richtext td p {
            margin: 0;
            padding: 0;
        }

        .custom-richtext-fullscreen {
            margin-top: 0;
            padding: 0;
        }
    </style>
    <title>{{$data['title']}}</title>
    <meta name="keywords" content="{{$data['title']}}">
    <meta name="description" content="{{$data['title']}}">
</head>
<body>
<div class="custom-richtext">
    {!! $data['description'] !!}
</div>
</body>
</html>