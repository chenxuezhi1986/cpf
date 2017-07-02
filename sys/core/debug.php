<style type="text/css">

        #debug_info h1, h2 {
            font-family: sans-serif;
            font-weight: normal;
            font-size: 0.9em;
            margin: 1px;
            padding: 0;
        }

        #debug_info h1 {
            margin: 0;
            text-align: left;
            padding: 2px;
            background-color: #f0c040;
            color: black;
            font-weight: bold;
            font-size: 1.2em;
        }

        #debug_info h2 {
            background-color: #9B410E;
            color: white;
            text-align: left;
            font-weight: bold;
            padding: 2px;
            border-top: 1px solid black;
        }

        #debug_info {
			margin-top:20px;
        }

        #debug_info pre {
            background: #f0ead8;
			margin:0px;
			padding:5px;
        }
    </style>

<div id="debug_info">
	<h1>Debug Console</h1>
	<?php foreach(Kernel::$debug_info as $key=>$val){ ?>
    <h2><?php echo $key;?></h2>
    <pre><?php "\r\n".var_export($val);?></pre>
    <?php } ?>
</div>