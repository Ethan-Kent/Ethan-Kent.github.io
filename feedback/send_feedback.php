<?php
// 存储提交次数的文件路径
$file = 'ubmit_count.txt';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = $_POST['contact'];
    $question = $_POST['question'];

    // 获取用户 IP 地址
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // 获取当前日期
    $currentDate = date('Y-m-d');

    // 读取文件内容，获取该 IP 当天的提交次数
    if (file_exists($file)) {
        $data = file_get_contents($file);
        $submitCounts = json_decode($data, true);
    } else {
        $submitCounts = [];
    }

    if (isset($submitCounts[$ipAddress][$currentDate])) {
        $submitCount = $submitCounts[$ipAddress][$currentDate];
    } else {
        $submitCount = 0;
    }

    if ($submitCount < 5) {
        // 获取浏览器信息
        $browser = $_SERVER['HTTP_USER_AGENT'];

        // QQ 邮箱的相关配置
        $to = "slyzmun@outlook.com"; 
        $subject = "用户反馈";
        $message = "联系方式：".$contact."\n问题描述：".$question."\nIP 地址：".$ipAddress."\n浏览器：".$browser;
        $headers = "From: your_qq_email@qq.com"; 

        // 设置 SMTP 服务器和授权码
        ini_set("SMTP","ssl://smtp-mail.outlook.com");
        ini_set("smtp_port","587");
        ini_set("auth_username","slyzmun@outlook.com");
        ini_set("auth_password","Slyz_Mun@1965");

        if (mail($to, $subject, $message, $headers)) {
            // 增加提交次数
            $submitCount++;
            $submitCounts[$ipAddress][$currentDate] = $submitCount;

            // 将更新后的提交次数写入文件
            file_put_contents($file, json_encode($submitCounts));

            echo "<div style='width: 100%; background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px; font-size: 24px;'>发送成功</div>";
        } else {
            echo "发送反馈时出错，请稍后再试。";
        }
    } else {
        echo "<div style='width: 100%; background-color: #FF5733; color: white; padding: 20px; text-align: center; border-radius: 5px; font-size: 24px;'>您今天的提交次数已达上限</div>";
    }
}
?>
