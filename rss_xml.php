<?php
$url = 'https://contoh.com/feed/';
$xml = file_get_contents($url);
$data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

$openai_api_key = 'YOUR_API_KEY';

foreach($data->channel->item as $item) {
  $title = $item->title;
  $description = $item->description;
  $image = $item->xpath('media:content')[0]['url'];
  $apply_url = 'https://contoh.com/apply/';

  // mengambil konten artikel dari RSS atau XML
  $content = $item->children('http://purl.org/rss/1.0/modules/content/')->encoded;

  // membuat koneksi ke API ChatGPT
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.openai.com/v1/engines/davinci-codex/completions",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode(array(
      "prompt" => $content,
      "max_tokens" => 1024,
      "temperature" => 0.7,
      "stop" => ["\n", "(", ")", "[", "]", "{", "}", ">", "___"],
      "model" => "davinci-codex-002"
    )),
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/json",
      "Authorization: Bearer " . $openai_api_key
    ),
  ));

  // mengirim permintaan ke API ChatGPT
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  if ($err) {
    echo "Error: " . $err;
  } else {
    // mengambil hasil dari API ChatGPT
    $response = json_decode($response, true);
    $content = $response['choices'][0]['text'];

    // memasukkan konten baru ke dalam WordPress
    $new_post = array(
      'post_title' => $title,
      'post_content' => $content,
      'post_status' => 'publish',
      'post_author' => 1,
      'post_category' => array(1),
      'meta_input' => array(
        'featured_image' => $image,
        'apply_url' => $apply_url
      )
    );
    wp_insert_post($new_post);
  }
}

