<?php
    $flex_template = [
        "type" => "bubble",
        "header" => [
            "type" => "box",
            "layout" => "vertical",
            "contents" => [
                [
                    "type" => "text",
                    "text" => (string) $nama,
                    "weight" => "bold",
                    "size" => "xl",
                    "color" => "#FFFFFF",
                    "wrap" => true
                ]
            ],
            "backgroundColor" => "#007bff"
        ],
        "body" => [
            "type" => "box",
            "layout" => "vertical",
            "contents" => [[
                    "type"  => "text",
                    "text"  => "Berikut adalah kasus covid-19 di ".(string) $nama,
                    "wrap"  => true
                ],
                [
                    "type" => "box",
                    "layout" => "horizontal",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "Positif",
                            "weight"  => "bold"
                        ],
                        [
                            "type" => "text",
                            "text" => (string) number_format($positif,0,".","."),
                            "weight" => "bold"
                        ]
                    ]
                ],
                [
                    "type" => "box",
                    "layout" => "horizontal",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "Sembuh",
                            "weight" => "bold"
                        ],
                        [
                            "type" => "text",
                            "text" => (string) number_format($sembuh,0,".","."),
                            "weight" => "bold"
                        ]
                    ]
                ],
                [
                    "type" => "box",
                    "layout" => "horizontal",
                    "contents" => [
                        [
                            "type" => "text",
                            "text" => "Meninggal",
                            "weight" => "bold"
                        ],
                        [
                            "type" => "text",
                            "text" => (string) number_format($meninggal,0,".","."),
                            "weight" => "bold"
                        ]
                    ]
                ]
            ]
        ]
    ];