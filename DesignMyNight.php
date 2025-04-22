<?php

class DesignMyNight {

    private $url = 'https://api.designmynight.com/v4/venues/5e4bf7a9d4ea511f3f3feb33,5e4bf7f3d4ea511da4214824,5e4bf7dbd4ea511eb57b1773';

    private $bearer = array(
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdjM2UxOTQ3MGMwNjFkN2Y2ZmMzYzQwZmExNWFiNDY3YjM5ODhlNGEwYjIzMTQ2MjVhY2FlOGYwM2VhOGQ0NmFkOTkyMzYxOTkzN2I5Nzc2In0.eyJhdWQiOiI1OWU3MjljNTJmOGU5ZDYyZTg3ZmMwNTIiLCJqdGkiOiI3YzNlMTk0NzBjMDYxZDdmNmZjM2M0MGZhMTVhYjQ2N2IzOTg4ZTRhMGIyMzE0NjI1YWNhZThmMDNlYThkNDZhZDk5MjM2MTk5MzdiOTc3NiIsImlhdCI6MTcyNjUzMTI2OCwibmJmIjoxNzI2NTMxMjY4LCJleHAiOjIwNDIwNjQwNjgsInN1YiI6IjY2ZThjNmM0NmQyMmU4NDg0ZjFjYTFiNCIsInNjb3BlcyI6W119.zPMw-EG-A3Zq0bgGxHfBoauOauOzgGbjl-YI-VLLQ1n-OBgYBRbxfpAmWeYxohMiBL9GuJfM3FfZrPh-mT2OtX86jgr9iFVSqmW8vh2wS13wM8djjUXiXlt_J8dTgFcIGKrnRI5HEIHCeKedo6EMlqsscMmNa3bAVgI2z3R1xXL3VNg2S9SpgtORW935JUug-B4VilEWGCQCbm4WwOgvF9f8Ylvg7bHrc-n-AA2_mVNJrM6zk6ZSH0zztH-gqYmT_YloyUt6kWBytVzWn7Nx7yyAuUln8ujxkj9DyrWcgDIwZeh-ylxFLJcRwhivIh6fVTs7XXnS74icdeQ22HAEa7ZuUZBQtT3txZfTW8IhggxVZF8BNkzZLRycPca9N5aMU9xloVVHptDWufki6MV5mDUhfO_-qH34codnKsz13po_oBuTsIM4y-DS3MTJkIF6175cgmbwjPXyi_jXpgrac_SGEWuAdtSaLMntm73emk9FL45RedcDUrIXInAVgCjYPZJSdg-Md4mud1pCINo5tGffMIcP1bh8D29nLaWECkCdytqrourj0MgJmym3bkrtjqBfZzRPLzy6-zFKnypPMO0744s5GG2YrDxN1hcUhrlItYrqCmbnVMt3gD1Mo8Lk7_S6FwASGcZ9htViSz_ch0-ex63Lkjc-IkOIMk6ERjg',
        'Cookie: current_region=london'
    );

    public function __construct() {}

    public function getVenues() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $this->bearer,
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        return  $response;
    }
}

