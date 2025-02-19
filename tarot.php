<?php

// استلام البيانات من نموذج HTML
$type = isset($_POST['readingType']) ? htmlspecialchars($_POST['readingType']) : ''; // نوع القراءة (عام، حب، مهنة)
$cardNames = isset($_POST['cardNames']) ? json_decode($_POST['cardNames'], true) : array(); // Decode the JSON string into an array

// التحقق من وجود البيانات المدخلة
if (empty($type) || empty($cardNames)) {
    echo json_encode(array('error' => 'البيانات المدخلة غير كاملة.'));
    exit;
}

$ip = $_SERVER['REMOTE_ADDR']; // الحصول على عنوان IP
$data = json_decode(file_get_contents('php://input'), true);
$ip = isset($data['ip']) ? $data['ip'] : $_SERVER['REMOTE_ADDR'];
$secretCode = isset($data['secretCode']) ? $data['secretCode'] : '';

// التحقق من الحد اليومي
$usageFile = 'ip_usage.json';
$maxRequests = 5;

if ($secretCode === '54m4li001') {
    // لا داعي للانتظار إذا كان الحد اليومي لم يتجاوز
    if (file_exists($usageFile)) {
        $usageData = json_decode(file_get_contents($usageFile), true);
    } else {
        $usageData = array();
    }
    
    if (!isset($usageData[$ip])) {
        $usageData[$ip] = array('count' => 0, 'last_used' => date('Y-m-d H:i:s'));
    }
    $usageData[$ip]['count'] = 0; // إعادة تعيين العد
    echo json_encode(array("limitExceeded" => false, "readingType" => "full"));
    file_put_contents($usageFile, json_encode($usageData));
    exit;
}

if (file_exists($usageFile)) {
    $usageData = json_decode(file_get_contents($usageFile), true);
} else {
    $usageData = array();
}

if (isset($usageData[$ip]) && $usageData[$ip]['count'] >= $maxRequests) {
    echo json_encode(array('limitExceeded' => true));
    exit;
}

// تحليل بطاقات التاروت باستخدام Gemini API 
$prompt = "أنت قارئ بطاقات تاروت متمرس، تُعرف بحكمتك العميقة وقدرتك على سبر أغوار النفس البشرية. لديك موهبة نادرة في قراءة الطالع وفهم دلالات رموز التاروت. اليوم، أمامك مجموعة من البطاقات تحمل رسائل خفية تحتاج إلى فك شفرتها: " . implode(', ', $cardNames) . ".";

// تخصيص الرسالة بناءً على نوع القراءة
switch ($type) {
    case 'love':
        $prompt .= " ركز على عالم الحب والعلاقات. انظر في خفايا القلوب، واستكشف طبيعة الروابط العاطفية، وحدد التحديات والفرص المتعلقة بالحب والزواج. هل هناك رسائل خفية تكشف عن مشاعر شخص ما نحوي؟ هل هناك نصائح أو تحذيرات يجب أن أنتبه لها في حياتي العاطفية؟";
        break;
    case 'career':
        $prompt .= " انصب تركيزك على الجوانب المهنية والعملية. ما هي الرسائل التي تحملها هذه البطاقات عن مساري المهني؟ هل هناك فرص جديدة تلوح في الأفق؟ ما هي التحديات التي قد تواجهني وكيف أتغلب عليها؟ هل هناك نصائح لتطوير مسيرتي المهنية وتحقيق النجاح؟";
        break;
    default:
        $prompt .= " أريد منك أن تقدم لي تحليلاً شاملاً لهذه البطاقات، يشمل جميع جوانب الحياة: الروحية، العاطفية، المهنية، والمادية. ما هي الرسائل العامة التي تحملها لي؟ ما هي الدروس التي يجب أن أتعلمها؟ ما هي النصائح التي توجهني نحو تحقيق التوازن والانسجام في حياتي؟";
        break;
}

$prompt .= " \n\n تذكر أنني أبحث عن إجابات واضحة ومحددة، وليست مجرد كلام عام. أريد منك أن تتحدث بصدق وصراحة، وأن تستخدم لغة قوية ومؤثرة تلامس أعماق روحي.";

// تحديث عدد مرات الاستخدام
if (isset($usageData[$ip])) {
    $usageData[$ip]['count']++;
} else {
    $usageData[$ip] = array('count' => 1, 'last_used' => date('Y-m-d H:i:s'));
}
file_put_contents($usageFile, json_encode($usageData));

// إرسال الطلب إلى Gemini API
$geminiApiKey = 'AIzaSyAP-2Q3kmQoJuAda6wPlLxqBHue7Almr7Q';
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $geminiApiKey;

$headers = array(
    'Content-Type: application/json'
);

$data = array(
    'contents' => array(
        array(
            'parts' => array(
                array(
                    'text' => $prompt
                )
            )
        )
    )
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    $errorMessage = curl_error($ch);
    echo json_encode(array('error' => 'حدث خطأ في الاتصال بـ Gemini API: ' . $errorMessage));
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

if (isset($responseData['error'])) {
    echo json_encode(array('error' => $responseData['error']['message']));
    exit;
}

if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $analysis = $responseData['candidates'][0]['content']['parts'][0]['text'];
    $formattedAnalysis = str_replace(array("\n\n", "**", "**"), array("</p><p>", "<strong>", "</strong>"), $analysis);
    echo $formattedAnalysis;
} else {
    echo json_encode(array('error' => 'لم يتمكن النموذج من تقديم التفسير.'));
}

// تسجيل الاستجابة في ملف log
file_put_contents("gemini_response.log", print_r($responseData, true));

?>
