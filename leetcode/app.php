<?php

function httpPostJson($url, array $params = [], bool $isHttps = false)
{
    $data = json_encode($params);
    // $extraheader = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8; Accept: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    if ($isHttps) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    $output = curl_exec($ch);

    $flag = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ((int) $flag >= 400) {
        return json_encode(['http_code' => $flag]);
    }

    return $output;
}

$data = file_get_contents("./data.json");
$data = json_decode($data, true);

$titles   = [];
$untitles = [];
foreach ($data['stat_status_pairs'] as $val) {
    // if((int)$val['stat']['question_id'] > 10){
    //     continue;
    // }
    if (!$val['paid_only']) {
        $titles[] = $val['stat']['question__title_slug'];
    } else {
        $untitles[] = $val['stat']['question__title_slug'];
    }
}

$url                    = "https://leetcode-cn.com/graphql";
$param                  = [];
$param['operationName'] = "questionData";
$param['variables']     = [
    'titleSlug' => 'two-sum',
];
$param['query'] = 'query questionData($titleSlug: String!) {
  question(titleSlug: $titleSlug) {
    questionId
    questionFrontendId
    boundTopicId
    title
    titleSlug
    content
    translatedTitle
    translatedContent
    isPaidOnly
    difficulty
    likes
    dislikes
    isLiked
    similarQuestions
    contributors {
      username
      profileUrl
      avatarUrl
      __typename
    }
    langToValidPlayground
    topicTags {
      name
      slug
      translatedName
      __typename
    }
    companyTagStats
    codeSnippets {
      lang
      langSlug
      code
      __typename
    }
    stats
    hints
    solution {
      id
      canSeeDetail
      __typename
    }
    status
    sampleTestCase
    metaData
    judgerAvailable
    judgeType
    mysqlSchemas
    enableRunCode
    enableTestMode
    envInfo
    book {
      id
      bookName
      pressName
      description
      bookImgUrl
      pressImgUrl
      productUrl
      __typename
    }
    isSubscribed
    __typename
  }
}';

// $ret = httpPostJson($url, $param, true);
//     $res = json_decode($ret,true);
// $question = $res['data']['question'];
// echo $question['content'];
// echo $question['translatedContent'];
// echo $question['translatedTitle'];
// echo $question['difficulty'];
// echo $question['questionFrontendId'];
// echo $question['topicTags'][0]['name'];
// echo $question['topicTags'][0]['translatedName'];
$i = 0;
foreach ($titles as $tva) {
    $param['variables'] = ['titleSlug' => $tva];
    $ret                = httpPostJson($url, $param, true);
    $res                = json_decode($ret, true);
    $question           = $res['data']['question'];
    $content            = $question['content'];
    $translatedContent  = $question['translatedContent'];
    $translatedTitle    = $question['translatedTitle'];
    $difficulty         = $question['difficulty'];
    $questionFrontendId = $question['questionFrontendId'];
    echo $questionFrontendId,"**";
    $dirs = [];
    if ($question['topicTags']) {
        $name  = $question['topicTags'][0]['name'];
        $tname = $question['topicTags'][0]['translatedName'];
        foreach ($question['topicTags'] as $dva) {
            if(!$dva['translatedName'] && $dva['name']){
                $dirs[] = "./doc/" . $dva['name'] . '/';
            }else if(!$dva['translatedName'] && !$dva['name']){
                $dirs[] = "./doc/other/";
            }else{
            	$dirs[] = "./doc/" . $dva['translatedName'] . '/';
            }
        }
        
        // $dir   = "./doc/" . $tname . '/';
        // $tags = '#### ' . $tname . '[' . $name . ']' . "\n\n\n ----- \n";

        $tags = '#### ' . array_reduce($question['topicTags'],
            function ($str, $item) {
                return $str .= $item['translatedName'] . '[' . $item['name'] . "]    ";
            }) . "\n\n";
    } else {
        $name  = '';
        $tname = '';
        $dirs[]   = "./doc/other/";
        $tags  = '';
    }
    // echo $tags;

    $fields = '## ' . $translatedTitle . "\n\n" .
        "**原链接**：<https://leetcode-cn.com/problems/" . $tva . "/>\n\n" . 
        $tags .
        '##### 难度：**`' . $difficulty .'`**' . "\n\n----- \n" . 
        $content . "\n\n----- \n" .
        $translatedContent;

    // $fields = doReplace($fields);
    foreach ($dirs as $dir) {
        if(strpos("L", $questionFrontendId) !== false){
            $file = $dir . $questionFrontendId . '-' . $translatedTitle . '.md';
        }else{
            $file = $dir . str_pad($questionFrontendId, 4, '0', STR_PAD_LEFT) . '-' . $translatedTitle . '.md';
        }

        if (is_dir($dir)) {

        } else {
            mkdir($dir);
        }
        file_put_contents($file, $fields);
    }
    $i++;
    // if($i == 10){
    // break;
    // }
}
echo "=======",$i;
