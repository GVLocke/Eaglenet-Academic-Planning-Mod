<?php
    function parseExpression($str) {
            $expression_array = array();
            $count = 0;
            while (strpos($str, "(") !== false && strpos($str, ")") !== false) {
                $str = preg_replace_callback('/\((.*?)\)/', function($matches) use (&$count, &$expression_array) {
                    $expression_array[] = parseInnerExpression($matches[1]);
                    // print_r($expression_array);
                    return $count++;
                }, $str);
            }
            $operators = ['and', 'or'];
            $top_op = "";
            foreach ($operators as $op) {
                if (strpos($str, $op) !== false) {
                    $top_op = $op;
                    break;
                }
            }
            return [
                "operator" => $top_op,
                "terms" => $expression_array
            ];
        }

    function parseInnerExpression($str) {
        if (strpos($str, "(") == false && strpos($str, ")") == false) {
            $operators = ['and', 'or'];
            foreach ($operators as $op) {
                if (strpos($str, $op) !== false) { // there's an operator inside
                    $term = explode($op, $str);
                    return [
                        "operator" => $op,
                        "terms" => $term
                    ];
                }
            }
            return [
                "operator" => 'none',
                "terms" => $str
            ];
        }
    }
?>