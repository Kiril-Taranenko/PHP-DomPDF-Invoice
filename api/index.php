<?php

    use Dompdf\Dompdf;
    use Dompdf\Options;
    require 'vendor/autoload.php';
    require_once 'qrcode.php';
    include 'barcode.php';


    $tmp_data_count = 0;
    function calcPos($tmpData)
    {

        $tmp_data_count = strlen($tmpData);

        switch ($tmpData[$tmp_data_count - 1]){
            case "0":
                $tmpData[$tmp_data_count - 1] = "0";
                break;
            case "1":
                $tmpData[$tmp_data_count - 1] = "0";
                break;
            case "2":
                $tmpData[$tmp_data_count - 1] = "0";
                break;
            case "3":
                $tmpData[$tmp_data_count - 1] = "5";
                break;
            case "4":
                $tmpData[$tmp_data_count - 1] = "5";
                break;
            case "6":
                $tmpData[$tmp_data_count - 1] = "5";
                break;
            case "7":
                $tmpData[$tmp_data_count - 1] = "5";
                break;
            case "8":
                $tmpData[$tmp_data_count - 1] = "0";
                (float)$tmpData + 0.1;
                $tmpData = strval(number_format($tmpData));
                break;
            case "9":
                $tmpData[$tmp_data_count - 1] = "0";
                (float)$tmpData + 0.1;
                $tmpData = strval(number_format($tmpData));
                break;
        }
        return $tmpData;


    }
    function matrix_gen($updated_dt, $birthday, $postcode, $total_invoice, $patient_date, $f_firstname)
    {

        $def = "1900-01-01";

        $matrix = "/-/#".strval(random_int(10, 99))."#";
        $mat_date = "";
        $tmp_date_arr = explode(" ", $updated_dt);
        $mat_date = $tmp_date_arr[0];
        $mat_date = str_replace(".", "", $mat_date);
        $tmp_date_t = str_replace(":", "", $tmp_date_arr[1]);
        $mat_date .= $tmp_date_t;
        $matrix .= $mat_date."#";

//
//        $def_date = strtotime($def_date);
//        $birth_date = strtotime($birthday);

        $def_date = new DateTime($def);
        $birth_date = new DateTime($birthday);
        $interval = $def_date->diff($birth_date);



        $days_tmpp = $interval->format('%R%a');
        $days_tmpp = str_replace("+", "", $days_tmpp);
        $days_tmpp = (int) $days_tmpp;


        $n_postcode = (int)$postcode;

        $tmp_inv_total = calcPos($total_invoice);
        $t_inv_total = round($tmp_inv_total);

        $low_date = "";
        $tmp_low_date = explode(".", $patient_date);

        for($j = count($tmp_low_date); $j > 0; $j--)
        {
            if($j == count($tmp_low_date))
            {
                $low_date = $tmp_low_date[$j - 1];
            }
            else{
                $low_date .= "-".$tmp_low_date[$j - 1];
            }

        }

        $lowest_days = new DateTime($low_date);
        $interval = $def_date->diff($lowest_days);
        $low_day_count = $interval->format('%R%a');
        $low_day_count = str_replace("+", "", $low_day_count);
        $low_day_count = (int) $low_day_count;

        $f_len = strlen($f_firstname);
        $f_first_num = substr($f_firstname, 1, $f_len-1);
        $f_first_num = (int) $f_first_num;

        $checking_num = (int)$days_tmpp + (int)$n_postcode + (int)$t_inv_total + (int)$low_day_count + (int)$f_first_num;
        $matrix .= strval($checking_num);

//        print_r($matrix);
//        exit();

        return $matrix;
    }
    function non_Permission($reason)
    {
        echo($reason);
        echo("<br />");
        echo "Behandlungsdatum oder Ansprechpartner fehlen. Bitte kontaktieren Sie uns.";
    }
    //    Get invoice id part
    $invoice_doc_nr = "";
    if(isset($_REQUEST['invoice_id']))
    {
        $invoice_doc_nr = $_REQUEST['invoice_id'];
    }
    else
    {
//        $invoice_doc_nr = "1030151321350152";
//        1030151321250154
//        93049
//        1653
//        1655
//        1729
        $invoice_doc_nr = "1030151321250154";
    }



    // Get invoice data part
    //
    //    Bexio.com
    //    user: T23@vitaminbox.ch
    //    password: Bexio2020
    //    Dev.Bexio.com
    //    app name : new_invoice
    //    des : Get invoice data.
    //    redirection url : http://localhost/invoice/api
    //    client id : 923e4487-7941-45c0-b755-c4a165e344cb
    //    client secret : SqI7IiJ1cZxOVVStuw7_VjZ9wygaUWj_y1l3T7WHNmvkE7V6f99q2ItL18U5l8-8X0hyXQhGowjxAfRgYjufbg
    //    token : eyJraWQiOiI2ZGM2YmJlOC1iMjZjLTExZTgtOGUwZC0wMjQyYWMxMTAwMDIiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJ0MjNAdml0YW1pbmJveC5jaCIsImxvZ2luX2lkIjoiMzI2YmMxMjItMWZmNS00ZjQ1LWI3NDMtMDQzNWIxNGM4YmY2IiwiY29tcGFueV9pZCI6ImR4cDJqYzFnZW45dCIsInVzZXJfaWQiOjI3NDg3NywiYXpwIjoiZXZlcmxhc3QtdG9rZW4tb2ZmaWNlLWNsaWVudCIsInNjb3BlIjoib3BlbmlkIHByb2ZpbGUgZW1haWwgYWxsIHRlY2huaWNhbCIsImlzcyI6Imh0dHBzOlwvXC9pZHAuYmV4aW8uY29tIiwiZXhwIjozMTkyODA2MTUwLCJpYXQiOjE2MTYwMDYxNTAsImNvbXBhbnlfdXNlcl9pZCI6MSwianRpIjoiZTYwOTRjMTEtYzJkNi00MTdiLWFlOGYtYmYwOTYyYzk3ZmNjIn0.WnPzj4atEmFTtDO_0KENmI2_7BH2ON3UDmsd8MqfKeyXd4Jo3--mGPV2_BstbEl2n9lR72TDutWH8hJMbqFXjwsBTcjYTdlXzGtqw37g46JoSgH1xok3L8pduPSVawc5QjxQYmZR8V63apbaefp9fy39De0k_gvUsOz9n-nGsOWNOFWZuSqAayrAk7CqguNFVXscJ5J3JEU2qSWy8WNgNx-kyptdJ6trkAezU0RuP6w1XnqrhtfERFD078S4vLQs4ayUY1i5lAwSOFHMCwy0b8qO4ZmwPpHjRiSCEOLWUnz3HSo-5MAMkgoQHXqcc9v3OckP1Px3SqllUsNQ-krcsWfboCZoaXvN11JlGsTj4Cgvjofiw4sK6yu3Jf_Iq2fp63dCIdok6vJ-0_-bh_C8JghQTxmTbJMPEO3nT7GgYCB1ntzhC3_WkCqR3VYBVTxrrWcXb3pC_WdZapvABZhgmbTh2Y5SSsc1Nv6Fzcn33wb0pNGzO5SdOOXUH7HJR_-oPCTiBCLOeRv_7YTYuSeCt68wlefB9C_3FSJV1cUAkZd74wBsXFhDZAvj0EOCtdNivM4zJiR07PrphvNTEw4keauRwFiFsLokET-Vk5exz3-TK6ZQLray1RTQaJdl5byT-dVldQzpkvuoA5YqfSWASOwnSAnwuhcnNsT_AqiSkak
    //    cpanel
    //    https://seth.metanet.ch:8443/login_up.php
    //    Kirill@globuli.info
    //    UCanDoIt!!!
    //
    //    test invoice data
    //    1030151321350151
    //    1003916132134916
    //    1030171321351171
    //    1030241321352241

    //000webhosting
    //JAMESJOHNSON1112020@outlook.com
    //
    //Bullet0812!


//  new token = eyJraWQiOiI2ZGM2YmJlOC1iMjZjLTExZTgtOGUwZC0wMjQyYWMxMTAwMDIiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJmdW5jdGlvbWVkQHByYXhpc2dsb2J1bGkuY2giLCJsb2dpbl9pZCI6IjQxMzRjNTA2LWM5NjAtMTFlOS1iMTYyLWE0YmYwMTFjZTg3MiIsImNvbXBhbnlfaWQiOiJia2p3emV3eGoxYXIiLCJ1c2VyX2lkIjo1MTYwMywiYXpwIjoiZXZlcmxhc3QtdG9rZW4tb2ZmaWNlLWNsaWVudCIsInNjb3BlIjoib3BlbmlkIHByb2ZpbGUgZW1haWwgYWxsIHRlY2huaWNhbCIsImlzcyI6Imh0dHBzOlwvXC9pZHAuYmV4aW8uY29tIiwiZXhwIjozMTkyMzU4NjYxLCJpYXQiOjE2MTU1NTg2NjEsImNvbXBhbnlfdXNlcl9pZCI6MSwianRpIjoiYWI5ZmJkNmItYTA1Mi00YzhjLWFiMDUtYWI5YTIyMGRhYjk3In0.nTqbm3r2RcChwfPPO_XaZhBq4UAMyxi4WXEssb7VBrPIYUw6tRItRrlpsQMu7v4ERCKIMF7_T5vM_BnQ3H_6EyEdVRT0RGxmWpFxhzw-Bsrv-eMLD3CAvYoADFudVeeapUoMdJhX4I4yEmBtm5THsUZbBo-ir7JjIKg859iJYy7Oaz86jA7Lw5nIabMUjS9nY5yGrVwUUFNn9MjwijkqTQupkFGxuyRboDDz_S82lmZUfLGVY6ibiL_mYvyeaY7j_aF8SkLtkR0Lqxadpsq8d5jadild0Jr7e2FKjNTo1nNPHVw4ofkpunBs1ju2Tl-R6v53DpOkXZ4vakhd3VMWCbVYZ_ElV9-pxN9I3yDSMiCRAGiEvPYvdY6QMgohG7yZ2DU-S9MZ2_SwvmBsaZs6n8bNZa_Zx1aK9tdvc2Y4qjmRiTGtUOCEyo7iKDVd8v4SR1OZHxOVDPrMOyIIEtfjRvfJ9JghIZUyJ1oxe7jQ6ertJIQ9EQkT_I6D1OzB3Fb9YSJIP8OI81nQXitTqvgvExr2pyYSQz58jYGio0EsUH7dHERYkZPJq9ZYh7ScrshTMfMNIaIdjZR-A_3dp3z0Hgq8vRkRi-TqG8hATVuRhd2buwPzFXaTdA2am9y2kaoaFbGSsIkKRpn2MlbHrmzszOUjz4K9xOj2gX-dwhsP7Xc
//    third_token = eyJraWQiOiI2ZGM2YmJlOC1iMjZjLTExZTgtOGUwZC0wMjQyYWMxMTAwMDIiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJmdW5jdGlvbWVkQHByYXhpc2dsb2J1bGkuY2giLCJsb2dpbl9pZCI6IjQxMzRjNTA2LWM5NjAtMTFlOS1iMTYyLWE0YmYwMTFjZTg3MiIsImNvbXBhbnlfaWQiOiJia2p3emV3eGoxYXIiLCJ1c2VyX2lkIjo1MTYwMywiYXpwIjoiZXZlcmxhc3QtdG9rZW4tb2ZmaWNlLWNsaWVudCIsInNjb3BlIjoib3BlbmlkIHByb2ZpbGUgZW1haWwgYWxsIHRlY2huaWNhbCIsImlzcyI6Imh0dHBzOlwvXC9pZHAuYmV4aW8uY29tIiwiZXhwIjozMTk0NTk4MjgxLCJpYXQiOjE2MTc3OTgyODEsImNvbXBhbnlfdXNlcl9pZCI6MSwianRpIjoiMjA3ZTczODUtOTMxNC00Yjk4LTk0YjctZjhiN2IwYTc4OWNmIn0.U3QQ6g-6-19E9sWpd8cULP9ku0C0tILHa_7cdRaKyB4CdluWNeiLcPD1Bgeb_ljcm0ks-xhU2kJ44wmmHfFJiCZmnR3eT09wb8iYsa0SenMrMdCb-_0C3eqhwE7D9qigLo5Z4uYnYOG5KzxHbH9wDCk9IoBstuBJ-_XfeOrtEz_SOpqZNKFoEiwpJJ17ClNuXxA0TbinS2TRxy8t12nP3kBuYOMdNmUxSp8UjLvZy8lWFVABFVq2xC1QA5Wp3G8hFr_Zdq5cLiHCAWY5nDW64XnJZAEUcyd1K1pBVnDI42x_djzuQtwv6DbAuwc3JVR-djfmIJBjj0sFQmVazadQOzfTPnSqPCivfpMGzIhlK7tHYLJjnokI0zT2O0ZFes69mTKE40MIVN9bfR-6OqSo20q5KFZEmB19z-aigEm_U70MNccmlIVMmdfA3xSTJNVoMPsOavr-tykgIi9TIMkoDCa6aVfZxEZN3magos2HmscQvDS5lYuWr-HtCZN1EBWzjz-N7kEK3p177bj2I7USsw31s_a0e5E4T5Z7OcKUi_CE18oEPUQy9lpmukQ2v6OtFe5zOcGVqEJ7PqROFokjqGgcmik521VJEMVJHiw-AH2Z7YGece44jMt3Y4qvHqD3A4YR-oSrjcXrXVKrsUQi8M8dQ8RXYlshuDa-n-S0Jng
$bearer_token = "eyJraWQiOiI2ZGM2YmJlOC1iMjZjLTExZTgtOGUwZC0wMjQyYWMxMTAwMDIiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJ0MjNAdml0YW1pbmJveC5jaCIsImxvZ2luX2lkIjoiMzI2YmMxMjItMWZmNS00ZjQ1LWI3NDMtMDQzNWIxNGM4YmY2IiwiY29tcGFueV9pZCI6ImR4cDJqYzFnZW45dCIsInVzZXJfaWQiOjI3NDg3NywiYXpwIjoiZXZlcmxhc3QtdG9rZW4tb2ZmaWNlLWNsaWVudCIsInNjb3BlIjoib3BlbmlkIHByb2ZpbGUgZW1haWwgYWxsIHRlY2huaWNhbCIsImlzcyI6Imh0dHBzOlwvXC9pZHAuYmV4aW8uY29tIiwiZXhwIjozMTkyODA2MTUwLCJpYXQiOjE2MTYwMDYxNTAsImNvbXBhbnlfdXNlcl9pZCI6MSwianRpIjoiZTYwOTRjMTEtYzJkNi00MTdiLWFlOGYtYmYwOTYyYzk3ZmNjIn0.WnPzj4atEmFTtDO_0KENmI2_7BH2ON3UDmsd8MqfKeyXd4Jo3--mGPV2_BstbEl2n9lR72TDutWH8hJMbqFXjwsBTcjYTdlXzGtqw37g46JoSgH1xok3L8pduPSVawc5QjxQYmZR8V63apbaefp9fy39De0k_gvUsOz9n-nGsOWNOFWZuSqAayrAk7CqguNFVXscJ5J3JEU2qSWy8WNgNx-kyptdJ6trkAezU0RuP6w1XnqrhtfERFD078S4vLQs4ayUY1i5lAwSOFHMCwy0b8qO4ZmwPpHjRiSCEOLWUnz3HSo-5MAMkgoQHXqcc9v3OckP1Px3SqllUsNQ-krcsWfboCZoaXvN11JlGsTj4Cgvjofiw4sK6yu3Jf_Iq2fp63dCIdok6vJ-0_-bh_C8JghQTxmTbJMPEO3nT7GgYCB1ntzhC3_WkCqR3VYBVTxrrWcXb3pC_WdZapvABZhgmbTh2Y5SSsc1Nv6Fzcn33wb0pNGzO5SdOOXUH7HJR_-oPCTiBCLOeRv_7YTYuSeCt68wlefB9C_3FSJV1cUAkZd74wBsXFhDZAvj0EOCtdNivM4zJiR07PrphvNTEw4keauRwFiFsLokET-Vk5exz3-TK6ZQLray1RTQaJdl5byT-dVldQzpkvuoA5YqfSWASOwnSAnwuhcnNsT_AqiSkak";


    $is_valid_time="09:10:20";

    $headers = array(
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$bearer_token,
    );

    $client = new \GuzzleHttp\Client();
    $url = 'https://api.bexio.com/2.0/kb_invoice';
    $tmp_inv_list = "";
    $invoice_list = "";



//    try {
//        $response = $client->request('GET', $url, array(
//            'headers' => $headers,
//        ));
//        $tmp_inv_list = $response->getBody()->getContents();
//    }
//    catch (\GuzzleHttp\Exception\BadResponseException $e) {
//        print_r($e->getMessage());
//    }

//    $invoice_list = json_decode($tmp_inv_list);
//    $n_invoice_count = count($invoice_list);

//    $n_tmp_index = 0;
//    $n_cur_index = 0;
//    for($n_tmp_index = 0; $n_tmp_index < $n_invoice_count; $n_tmp_index++)
//    {
//        if($invoice_list[$n_tmp_index]->document_nr == $invoice_doc_nr)
//        {
//            $n_cur_index = $n_tmp_index;
//        }
//
//    }
//
//    $invoice_id = $invoice_list[$n_cur_index]->id;



    $invoice_data="";
    $contact_data = "";
    $company_data = "";
    $user_data = "";
    $bank_data = "";

/////// Get invoice data //////////
//    print_r("============================================= invoice data =========================================== \n\n");
//    $url = 'https://api.bexio.com/2.0/kb_invoice/'.$invoice_id;
    $url = 'https://api.bexio.com/2.0/kb_invoice/search';
    $request_body = '[
        {
            "field": "document_nr",
            "value": ' .$invoice_doc_nr.',
            "criteria": "="
        }
    ]';

    try {
            $response = $client->request('POST', $url, array(
                'headers' => $headers,
                'body' => $request_body,
            ));
        $invoice_data = json_decode($response->getBody()->getContents());
        $invoice_id = $invoice_data[0]->id;
     }
    catch (\GuzzleHttp\Exception\BadResponseException $e) {
        print_r($e->getMessage());
    }
/////// End invoice //////////
//print_r($invoice_id);
//exit();

/////// Get Contact info /////////
//    print_r("\n\n============================================= Contact data =========================================== \n\n");
    $url = 'https://api.bexio.com/2.0/contact/'.$invoice_data[0]->contact_id;

    try {
        $response = $client->request('GET', $url, array(
            'headers' => $headers,
        ));

        $contact_data = json_decode($response->getBody()->getContents());
    }
    catch (\GuzzleHttp\Exception\BadResponseException $e) {
        print_r($e->getMessage());
}
/////// End Contact info /////////




    /////// Get User info /////////
    //print_r("\n\n============================================= user data =========================================== \n\n");
//    $url = 'https://api.bexio.com/3.0/users/'.$invoice_data[0]->user_id;
    $url = 'https://api.bexio.com/3.0/fictional_users/'.$invoice_data[0]->user_id;

    try {
        $response = $client->request('GET', $url, array(
            'headers' => $headers,
        ));

        $user_data = json_decode($response->getBody()->getContents());

    }
    catch (\GuzzleHttp\Exception\BadResponseException $e) {
//        print_r($e->getMessage());
        if($e->getResponse()->getStatusCode() == "404");
        {
            non_Permission("User data");
            exit();
        }

    }
    /////// End User info /////////




    /////// Get Company info /////////
    //print_r("\n\n============================================= Company data =========================================== \n\n");
    $url = 'https://api.bexio.com/2.0/company_profile';
    try {
        $response = $client->request('GET', $url, array(
            'headers' => $headers,
        ));

        $company_data = $response->getBody()->getContents();
        $company_data = json_decode($company_data);

    }
    catch (\GuzzleHttp\Exception\BadResponseException $e) {
        // handle exception or api errors.
        print_r($e->getMessage());
    }
    /////// End Company info /////////




    /////// Get Bank info /////////
    //print_r("\n\n============================================= Bank data =========================================== \n\n");
    $url = 'https://api.bexio.com/3.0/banking/accounts/'.$invoice_data[0]->bank_account_id;
    try {
        $response = $client->request('GET', $url, array(
            'headers' => $headers,
        ));

        $bank_data = json_decode($response->getBody()->getContents());

    }
    catch (\GuzzleHttp\Exception\BadResponseException $e) {
        print_r($e->getMessage());
    }
    /////// End Bank info /////////

    $valid_date = $invoice_data[0]->is_valid_from;
    $valid_date = date("d.m.Y", strtotime($valid_date));

    $contact_birth = $contact_data -> birthday;
    $contact_birth = date("d.m.Y", strtotime($contact_birth));

    $tmp_poses = "";
    //////// Position Info //////////////
    $url = 'https://api.bexio.com/2.0/kb_invoice/'.$invoice_data[0]->id.'/kb_position_article';

    try {
        $response = $client->request('GET', $url, array(
            'headers' => $headers,
        ));

//        print_r($response->getBody()->getContents());
        $tmp_poses = json_decode($response->getBody()->getContents());
    }
    catch (\GuzzleHttp\Exception\BadResponseException $e) {
    // handle exception or api errors.
        print_r($e->getMessage());
    }
    ////////// End Position //////////////


//    $tmp_poses = $invoice_data[0]->positions;
    $str_pos = "";
    $position_count = 0;
    $position_total_value = 0.00;

    $tm_pos_date_array = array();

    $two_p_f = 0.0;
    $sev_p_s = 0.0;
    foreach($tmp_poses as $tmp_pos)
    {



        $str_tmp_product = "";

        $tmp_pos_text = $tmp_pos->text;
        $tmp_pos_text_len = strlen($tmp_pos_text);
        $tmp_splite = explode(" ", $tmp_pos_text);
//        $tmp_pos_text_date = $tmp_splite[count($tmp_splite) - 1];
        $tmp_pos_text_date = substr($tmp_pos_text, $tmp_pos_text_len - 10, $tmp_pos_text_len);


//        $string = str_replace(" ".$tmp_pos_text_date,"",$tmp_pos_text);
        $string = substr($tmp_pos_text, 0, $tmp_pos_text_len - 11);
        $string = str_replace("<strong>","",$string);
        $string = str_replace("</strong><br />"," ",$string);


        $tmp_pro_code = explode(" ", $string);

//        $i = 0;
        for($i = 0; $i < count($tmp_pro_code); $i ++)
        {
            if($i != 0)
            {
                $str_tmp_product.= $tmp_pro_code[$i]." ";
            }
        }

        $tmp_totalvalue = number_format($tmp_pos->position_total, 2);
        if($tmp_pos->tax_value == "2.50")
        {
            $two_p_f += $tmp_totalvalue;
        }
        else{
            $sev_p_s += $tmp_totalvalue;
        }
//        print_r($tmp_pos->position_total);
//        print_r("<br />");
        $position_total_value += $tmp_pos->position_total;
        array_push($tm_pos_date_array, $tmp_pos_text_date);

        $str_pos .= "<table class='mytable mytable-body' style='border:none'>
                                <td width='10%'>".$tmp_pos_text_date."</td>
                                <td width='5%'>590</td>
                                <td width='57%' style='font-weight: bold;'>".$tmp_pro_code[0]."</td>
                                <td width='5%' style='text-align:right; font-size: 11px;'>".number_format($tmp_pos->amount, 2)."</td>
                                <td width='9%' style='text-align:right; font-size: 11px;'>".number_format($tmp_pos->unit_price, 2)."</td>
                                <td width='4%' style='text-align:right; font-size: 11px;'>1.00</td>
                                <td width='5%' style='text-align:right; font-size: 11px;'>".$tmp_pos->tax_value."%</td>
                                <td width='5%' style='text-align:right; font-size: 11px;'>".$tmp_totalvalue."</td>
                            </table>
                            <table class='mytable mytable-body' style='border:none'>
                                <td width='10%'></td>
                                <td width='5%'></td>
                                <td width='57%'>".$str_tmp_product."</td>
                                <td width='5%'></td>
                                <td width='9%'> </td>
                                <td width='4%'></td>
                                <td width='5%'></td>
                                <td width='5%'></td>
                            </table>
                            ";
        $position_count ++;
    }

//    var_dump($str_tmp_product);
//    exit();

    $position_total_value = number_format($position_total_value, 2);

//    $date_from = strtotime($invoice_data[0]->is_valid_from);
//    $date_to = strtotime($invoice_data[0]->is_valid_to);


    $date_from = new DateTime($invoice_data[0]->is_valid_from);
    $date_to = new DateTime($invoice_data[0]->is_valid_to);
    $interval_diff = $date_from->diff($date_to);


    $days = $interval_diff->format('%R%a');
    $days = str_replace("+", "", $days);
    $days = (int) $days;


//    $diff = abs($date_to - $date_from);
//    $years = floor($diff / (365*60*60*24));
//    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
//    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

    $instead_tax_array = array("percentage"=>"0", "value"=>"0");

    $instead_tax_array = json_encode($instead_tax_array);
    $instead_tax_array = json_decode($instead_tax_array);
    $tmp_taxes = $invoice_data[0]->taxs;

    if(count($tmp_taxes) == 1)
    {
        if($tmp_taxes[0]->percentage == "2.50")
        {
            array_push($tmp_taxes, $instead_tax_array);
        }
        else
        {
            array_push($tmp_taxes, $tmp_taxes[0]);
            $tmp_taxes[0] = $instead_tax_array;
        }
    }


    $round_tax_value_f = number_format($tmp_taxes[0]->value, 2);
    $round_tax_value_s = number_format($tmp_taxes[1]->value, 2);

    $f_betrag;
    $s_betrag;

    if($tmp_taxes[0]->value == "0")
        $f_betrag = number_format(0, 2);
    else
    {
        $f_betrag = $round_tax_value_f * (100 + $tmp_taxes[0]->percentage) / $tmp_taxes[0]->percentage;
    }
    if($tmp_taxes[1]->value = "1")
    {
        $s_betrag = number_format(0, 2);
    }
    else{
        $s_betrag = $round_tax_value_s * (100 + $tmp_taxes[1]->percentage) / $tmp_taxes[1]->percentage;
    }


    $dot_index = 0;

    $f_betrag = strval($f_betrag);
    $dot_index = strpos($f_betrag, ".");
    $f_betrag = substr($f_betrag, 0, $dot_index + 3);

    $s_betrag = strval($s_betrag);
    $dot_index = strpos($s_betrag, ".");
    $s_betrag = substr($s_betrag, 0, $dot_index + 3);

    $f_betrag = calcPos($f_betrag);
    $s_betrag = calcPos($s_betrag);


    for($i = 0; $i < $position_count; $i++)
    {
        if (!DateTime::createFromFormat('d.m.Y', $tm_pos_date_array[$i]) !== false) {
            non_Permission("Date format   ==> ".$tm_pos_date_array[$i]);
            exit();
        }
    }

    usort($tm_pos_date_array, function($a, $b) {
        $dateTimestamp1 = strtotime($a);
        $dateTimestamp2 = strtotime($b);

        return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
    });
    $lowest_date = $tm_pos_date_array[0];
    $highest_date = $tm_pos_date_array[count($tm_pos_date_array) - 1];

    $str_matrix_code = matrix_gen($valid_date." ".$is_valid_time, $contact_data->birthday, $contact_data->postcode, strval($position_total_value), $lowest_date, $user_data->firstname);


    $two_p_f = number_format($two_p_f, 2);
    $sev_p_s = number_format($sev_p_s, 2);


    $mwst_f = 0.00;
    $mwst_s = 0.00;


    $mwst_f = number_format(round($two_p_f/102.5 * 2.5, 2), 2);
    $mwst_s = number_format(round($sev_p_s/107.7 * 7.7, 2), 2);

//    print_r($str_matrix_code);
////
//    exit();

    $filename = "tmpSVG.svg";
    $format = "png";
    $symbology = "dmtx";
    $data = "/-/#15#20062016020643#896586";
    $bar_options = ["w" => 44, "h" => 44];
    $tmp_value = "/-/#15#20062016020643#896586";
    $generator = new barcode_generator();
    $svg = $generator->render_svg($symbology, $str_matrix_code, $bar_options);
    file_put_contents($filename, $svg);



// Generate PDF part
    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $options->set('tempDir', './pdf-export-tmp');

    $domPdf = new Dompdf($options);
    $html_string = "<html>
                        <head>
                            <style>
                                @page{
                                    margin: 0cm 1cm 0cm 1cm;
                                }
                                @font-face {
                                  font-family: 'arial';
                                  font-style: normal;
                                  font-weight: normal;
                                  src: url(http://" . $_SERVER['SERVER_NAME']."/invoice/api/font/arialmt.ttf) format('truetype');
                                }
                                @font-face {
                                  font-family: 'arial';
                                  font-style: normal;
                                  font-weight: bold;
                                  src: url(http://" . $_SERVER['SERVER_NAME']."/invoice/api/font/ARIALBOLDMT.OTF) format('truetype');
                                }
                                html {
                                    font-family: 'arial';
                                }
                                #main {
                                    margin-top:0.9cm;
                                    width: 100% ;
                                    font-size: 12px;
                                    page-break-after: always;
                                    
                                }
                                .mytable {
                                    border-collapse: collapse;
                                    width: 100%;
                                }
                                .mytable-head {
                                    border: 1px solid black;
                                    margin-bottom: 0;
                                    padding-bottom: 1px;
                                    border-bottom: none;
                                }
                                .mytable-body {
                                    border: 1px solid black;
                                    border-top: none;
                                    border-bottom: none;
                                    border-top: 0;
                                    margin-top: 0px;
                                    padding-top: 0px;
                                    padding-bottom: 1px;
                                    
                                }
                                .mytable-body td {
                                    border-top: 0;
                                    padding:0px;
                                    padding-left:1px;
                                    padding-bottom:1px;
                                    margin:0px;
                                    height: auto;                                    
                                    margin-top:-2px;
                                }
                                .mytable-footer {
                                    border-top: 0;
                                    margin-top: 0;
                                    padding-top: 0;
                                    border: 1px solid black;
                                    border-top:none;
                                }
                                .mytable-footer td {
                                    border-top: 0;
                                }
                                .footer {   
                                    position:fixed;                                
                                    bottom: 0.5cm; 
                                    left: 0cm; 
                                    right: 0cm;
                                    height: 2cm;
                                    width:100%;
                                }
                                .footer_s {
                                    position: fixed; 
                                    bottom: 3.8cm; 
                                    left: 0cm; 
                                    right: 0cm;
                                    clear:both;
                                    width:100%;
                                }
                                
                            </style>
                        </head>
                        <body>
                        <div id='main'>
                            <span style='margin-right: 15px; font-size:24px;'>TG<span style='font-size: 14pt; vertical-align: top; padding-top:4px;'>RechnungKopie für den Patienten</span></span>
                            <table class='mytable mytable-head'>
                                <td width='10%' style='padding-left: 3px; padding-top:0px;'>Dokument</td>
                                <td width='15%'>Identifikation</td>
                                <td width='15%'>".$invoice_data[0]->document_nr."</td>
                                <td width='25%'>".$valid_date." " .$is_valid_time. "</td>
                                <td width='35%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='10%' style='padding-left: 3px;'>Rechnung</td>
                                <td width='20%'>GLN-Nr.</td>
                                <td width='30%'>".$company_data[0]->name." / ".$user_data->lastname."</td>
                                <td width='10%'>Tel: </td>
                                <td width='30%'>".$company_data[0]->phone_fixed."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='10%' style='padding-left: 3px;'>ssteller</td>
                                <td width='20%'>ZSR-Nr. ".$user_data->firstname."</td>
                                <td width='30%'>".$company_data[0]->city."</td>
                                <td width='10%'>Email: </td>
                                <td width='30%'>".$company_data[0]->mail."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='10%' style='padding-left: 3px;'>Leistungse</td>
                                <td width='20%'>GLN-Nr.</td>
                                <td width='30%'>".$company_data[0]->name." / ".$user_data->lastname."</td>
                                <td width='10%'>Tel: </td>
                                <td width='30%'>".$company_data[0]->phone_fixed."</td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='10%' style='padding-left: 3px;'>rbringer</td>
                                <td width='20%'>ZSR-Nr. ".$user_data->firstname."</td>
                                <td width='30%'>".$company_data[0]->city."</td>
                                <td width='10%'>Email: </td>
                                <td width='30%'>".$company_data[0]->mail."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%' style='padding-left: 3px;'>Patient</td>
                                <td width='20%'>Name</td>
                                <td width='30%'>".$contact_data->name_1."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Vorname</td>
                                <td width='30%'>".$contact_data->name_2."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Strasse</td>
                                <td width='30%'>".$contact_data->address."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>PLZ</td>
                                <td width='30%'>".$contact_data->postcode."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Ort</td>
                                <td width='40%'>".$contact_data->city."</td>
                                <td width='20%' style='margin-left:150px;'>".$contact_data->name_2." ".$contact_data->name_1."</td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Geburtsdatum</td>
                                <td width='40%'>".$contact_birth."</td>
                                <td width='20%' style='margin-left:150px;'>".$contact_data->address."</td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Gesetz</td>
                                <td width='40%'>VVG</td>
                                <td width='20%' style='margin-left:150px;'>".$contact_data->postcode." ".$contact_data->city."</td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Geschlecht</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Falldatum</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Patienten-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>AHV-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>VEKA-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Versicherten-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Kanton</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Kopie</td>
                                <td width='30%'></td>
                                <td width='20%'>KoGu-Datum/Nr.</td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Vergütungsart</td>
                                <td width='30%'>TG</td>
                                <td width='20%'>Rechnungs-Datum/Nr.</td>
                                <td width='30%'>".$valid_date." / ".$invoice_data[0]->document_nr."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Vertrags-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'>Mahnungs-Datum/Nr.</td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Behandlung</td>
                                <td width='30%'>".$lowest_date." - ".$highest_date."</td>
                                <td width='20%'>Behandlungsgrund</td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='7%'></td>
                                <td width='20%'>Rolle/Ort /</td>
                                <td width='30%'>Komplementärmedizin / Praxis</td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='40%' style='padding-left: 3px; padding-bottom: 2px;'>Zuweiser</td>
                                <td width='20%' style='padding-bottom: 2px;'>GLN</td>
                                <td width='20%' style='padding-bottom: 2px;'>ZSR</td>
                                <td width='20%' style='padding-bottom: 2px;'>Name</td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='40%' style='padding-left: 3px;'>Diagnose</td>
                                <td width='20%'></td>
                                <td width='20%'></td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='15%' style='padding-left: 3px;'>Therapie</td>
                                <td width='40%'>".$invoice_data[0]->title."</td>
                                <td width='35%'>Taxpunktwert (TPW)</td>
                                <td width='10%'>MWST</td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='10%' style='padding-left: 3px;'>Bemerkungen</td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border:none'>
                                <td width='10%'>Datum</td>
                                <td width='5%'>Tarif</td>
                                <td width='57%'>Tarifziffer</td>
                                <td width='5%' style='text-align:right;'>Anzahl</td>
                                <td width='9%' style='text-align:right;'>Einzelpreis </td>
                                <td width='4%' style='text-align:right;'>TPW</td>
                                <td width='5%' style='text-align:right;'>MWST</td>
                                <td width='5%' style='text-align:right;'>Betrag</td>
                            </table>".$str_pos."
                            <div class='footer'>
                                <span style='margin-left:40%;'>Anzahlung &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".number_format($invoice_data[0]->total_received_payments, 2)." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style='font-weight: bold;'>Fälliger Betrag</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$position_total_value."</span>
                                <div>
                                    <div style='height:60px; width:60px; float:left; '>
                                        <img src='http://".$_SERVER['SERVER_NAME']."/invoice/api/tmpSVG.svg'/>
                                    </div>
                                    <div style='height:60px; width:100%; margin-top:-2px;'>
                                        <div style='margin-left:60px'>
                                            <table class='mytable mytable-body' style='border:none'>
                                                <td width='45%'>IBAN &nbsp; ".$bank_data->iban_nr."</td>
                                                <td width='15%'>Währung CHF </td>
                                                <td width='10%'>MWST-Nr. </td>
                                                <td width='30%'>".$company_data[0]->ust_id_nr."</td>
                                            </table>
                                            <table class='mytable mytable-body' style='border:none'>
                                                <td width='50%'>Identifikation/Kontocode</td>
                                                <td width='50%'>Teilnehmer/Konto-Nr. ".$bank_data->bank_account_nr." </td>
                                            </table>
                                            <table class='mytable mytable-body' style='border:none'>
                                                <td width='50%'>Zahlungsfrist (Tage, rein netto) ".$days."</td>
                                                <td width='50%'></td>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id='main'>
                            <span style='font-size: 15px; vertical-align: top'>Rückforderungsbeleg, Exemplar für den Versicherer</span>
                            <table class='mytable mytable-head'>
                                <td width='10%' style='padding-left: 3px; padding-top:0px;'>Dokument</td>
                                <td width='15%'>Identifikation</td>
                                <td width='15%'>".$invoice_data[0]->document_nr."</td>
                                <td width='25%'>".$valid_date." " .$is_valid_time. "</td>
                                <td width='35%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='10%' style='padding-left: 3px;'>Rechnung</td>
                                <td width='20%'>GLN-Nr.</td>
                                <td width='30%'>".$company_data[0]->name." / ".$user_data->lastname."</td>
                                <td width='10%'>Tel: </td>
                                <td width='30%'>".$company_data[0]->phone_fixed."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='10%' style='padding-left: 3px;'>ssteller</td>
                                <td width='20%'>ZSR-Nr. ".$user_data->firstname."</td>
                                <td width='30%'>".$company_data[0]->city."</td>
                                <td width='10%'>Email: </td>
                                <td width='30%'>".$company_data[0]->mail."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='10%' style='padding-left: 3px;'>Leistungse</td>
                                <td width='20%'>GLN-Nr.</td>
                                <td width='30%'>".$company_data[0]->name." / ".$user_data->lastname."</td>
                                <td width='10%'>Tel: </td>
                                <td width='30%'>".$company_data[0]->phone_fixed."</td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='10%' style='padding-left: 3px;'>rbringer</td>
                                <td width='20%'>ZSR-Nr. ".$user_data->firstname."</td>
                                <td width='30%'>".$company_data[0]->city."</td>
                                <td width='10%'>Email: </td>
                                <td width='30%'>".$company_data[0]->mail."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%' style='padding-left: 3px;'>Patient</td>
                                <td width='20%'>Name</td>
                                <td width='30%'>".$contact_data->name_1."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Vorname</td>
                                <td width='30%'>".$contact_data->name_2."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Strasse</td>
                                <td width='30%'>".$contact_data->address."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>PLZ</td>
                                <td width='30%'>".$contact_data->postcode."</td>
                                <td width='50%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Ort</td>
                                <td width='40%'>".$contact_data->city."</td>
                                <td width='20%' style='margin-left:150px;'>".$contact_data->name_2." ".$contact_data->name_1."</td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Geburtsdatum</td>
                                <td width='40%'>".$contact_birth."</td>
                                <td width='20%' style='margin-left:150px;'>".$contact_data->address."</td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Gesetz</td>
                                <td width='40%'>VVG</td>
                                <td width='20%' style='margin-left:150px;'>".$contact_data->postcode." ".$contact_data->city."</td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Geschlecht</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Falldatum</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Patienten-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>AHV-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>VEKA-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Versicherten-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Kanton</td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Kopie</td>
                                <td width='30%'></td>
                                <td width='20%'>KoGu-Datum/Nr.</td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Vergütungsart</td>
                                <td width='30%'>TG</td>
                                <td width='20%'>Rechnungs-Datum/Nr.</td>
                                <td width='30%'>".$valid_date." / ".$invoice_data[0]->document_nr."</td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Vertrags-Nr.</td>
                                <td width='30%'></td>
                                <td width='20%'>Mahnungs-Datum/Nr.</td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body'>
                                <td width='7%'></td>
                                <td width='20%'>Behandlung</td>
                                <td width='30%'>".$lowest_date." - ".$highest_date."</td>
                                <td width='20%'>Behandlungsgrund</td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='7%'></td>
                                <td width='20%'>Rolle/Ort /</td>
                                <td width='30%'>Komplementärmedizin / Praxis</td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='40%' style='padding-left: 3px;'>Zuweiser</td>
                                <td width='20%'>GLN</td>
                                <td width='20%'>ZSR</td>
                                <td width='20%'>Name</td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='40%' style='padding-left: 3px;'>Diagnose</td>
                                <td width='20%'></td>
                                <td width='20%'></td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='15%' style='padding-left: 3px;'>Therapie</td>
                                <td width='40%'>".$invoice_data[0]->title."</td>
                                <td width='35%'>Taxpunktwert (TPW)</td>
                                <td width='10%'>MWST</td>
                            </table>
                            <table class='mytable mytable-body' style='border-bottom: 1px solid black'>
                                <td width='10%' style='padding-left: 3px;'>Bemerkungen</td>
                                <td width='20%'></td>
                                <td width='30%'></td>
                                <td width='20%'></td>
                                <td width='20%'></td>
                            </table>
                            <table class='mytable mytable-body' style='border:none'>
                                <td width='10%'>Datum</td>
                                <td width='5%'>Tarif</td>
                                <td width='50%'>Tarifziffer</td>
                                <td width='6%' style='text-align:right;'>Anzahl</td>
                                <td width='12%' style='text-align:right;'>Einzelpreis </td>
                                <td width='6%' style='text-align:right;'>TPW</td>
                                <td width='6%' style='text-align:right;'>MWST</td>
                                <td width='6%' style='text-align:right;'>Betrag</td>
                            </table>".$str_pos."
                            <div class='footer_s'>
                                <div>
                                    <div style='height:60px; width:100%; bottom:3cm; margin-bottom:10px;'>
                                        <table class='mytable mytable-body' style='border:none'>
                                            <td width='5%'>Code</td>
                                            <td width='5%' style='text-align: right;'>Satz</td>
                                            <td width='8%' style='text-align: right;'>Betrag</td>
                                            <td width='8%' style='text-align: right;'>MWST</td>
                                            <td width='15%' style='padding-left: 15px;'>MWST-Nr</td>
                                            <td width='40%'>".$company_data[0]->ust_id_nr."</td>
                                            <td width='13%'>Gesamtbetrag</td>
                                            <td width='6%'>".$position_total_value."</td>
                                        </table>
                                        <table class='mytable mytable-body' style='border:none'>
                                            <td width='5%' style='text-align: center;'>0</td>
                                            <td width='5%' style='text-align: right;'>0.00</td>
                                            <td width='8%' style='text-align: right;'>0.00</td>
                                            <td width='8%' style='text-align: right;'>0.00</td>
                                            <td width='15%' style='padding-left: 15px;'>Währung</td>
                                            <td width='40%'>CHF</td>
                                            <td width='13%'></td>
                                            <td width='6%'></td>
                                        </table>
                                        <table class='mytable mytable-body' style='border:none'>
                                            <td width='5%' style='text-align: center;'>1</td>
                                            <td width='5%' style='text-align: right;'>".number_format($tmp_taxes[0]->percentage, 2)."</td>
                                            <td width='8%' style='text-align: right;'>".$two_p_f."</td>
                                            <td width='8%' style='text-align: right;'>".$mwst_f."</td>
                                            <td width='15%' style='padding-left: 15px;'>IBAN</td>
                                            <td width='40%'>".$bank_data->iban_nr."</td>
                                            <td width='13%'></td>
                                            <td width='6%'></td>
                                        </table>
                                        <table class='mytable mytable-body' style='border:none'>
                                            <td width='5%' style='text-align: center;'>2</td>
                                            <td width='5%' style='text-align: right;'>".$tmp_taxes[1]->percentage."</td>
                                            <td width='8%' style='text-align: right;'>".$sev_p_s."</td>
                                            <td width='8%' style='text-align: right;'>".$mwst_s."</td>
                                            <td width='37%' style='font-weight:bold; padding-left: 15px;'>Zahlbar innert ".$days." Tagen rein netto</td>
                                            <td width='13%'>Anzahlung</td>
                                            <td width='5%'>".number_format($invoice_data[0]->total_received_payments, 2)."</td>
                                            <td width='13%'>Fälliger Betrag</td>
                                            <td width='6%'>".$position_total_value."</td>
                                        </table>
                                    </div>
                                    <div style='height:60px; width:60px; margin-left:-5px;'>
                                        <img src='http://".$_SERVER['SERVER_NAME']."/invoice/api/tmpSVG.svg'/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";
    $html_string = preg_replace('/>\s+</', "><", $html_string);
    $domPdf->loadHtml($html_string);


    $domPdf->setPaper('A4', 'portrait');
    $domPdf->render();
    $domPdf->stream();

    unlink($filename);
