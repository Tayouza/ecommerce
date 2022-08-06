<?php

header("Content:Type: Application/json");

echo json_encode([
    "pessoas"=> [
        [
            "nome"=>"Taylor",
            "idade"=>"25",
            "sexo"=>"masc"
        ],
        [
            "nome"=>"Ruth",
            "idade"=>"24",
            "sexo"=>"fem"
        ]
    ]
]);
