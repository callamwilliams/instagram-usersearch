<?php

    class Instagram {

        public $result;
        public $error = false;
        private $token = ''; //public access token here

        public function __construct() {
            $this->count = $count;

            try {

                $this->result = json_decode($this->fetch('https://api.instagram.com/v1/users/search?q='.$this->user_id.'&access_token='.$this->token));

                if(isset($this->result->meta->error_message)) {
                    $this->error = $this->result->meta->error_message;
                } else {
                    $this->result = $this->result->data;
                }

            } catch(Exception $e) {
                $this->error = 'Unable to Fetch Data from Instagram';
            };
        }

        public function fetch($url) {

            try {
                $last_modified = filemtime(DOCROOT.'/data/instagram.json');
                if(time() - $last_modified < (60 * 15)) {
                    $result = file_get_contents(DOCROOT.'/data/instagram.json');
                    return $result;
                }

            } catch(Exception $e){};

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($ch);
            curl_close($ch);

            file_put_contents(DOCROOT.'/data/instagram.json', $result);

            return $result;
        }

    }

    $insta = new Instagram();

?>

<div class="instagram">

    <form action="" method="POST" >
        <p>
            <label for="username">Instagram Username</label>
            <input type="text" name="user_id" required="" id="user_id" value="" class="formbox" placeholder="Username" autocomplete="off">
            <button type="submit" class="btn btn--green" title="Submit">Submit</button>
        </p>
    </form>

    <? if(!empty($_POST) && isset($insta->result->data)): ?>

        <table class="table">
            <thead>
                <tr>
                    <th>Results</th>
                    <th>Username</th>
                    <th>User ID</th>
                    <th>Copy ID</th>
                </tr>
            </thead>
            <tbody>

            <? foreach ($insta->result->data as $key => $data): ?>

                    <tr>
                        <td><img src="<?= $data->profile_picture; ?>" alt="<?= $data->username; ?>" /></td>
                        <td><?= $data->username; ?></td>
                        <td ><?= $data->id; ?></td>
                        <td><button class="btn js-btn" data-clipboard data-clipboard-text="<?= $data->id; ?>">Copy</button></td>
                    </tr>

           <? endforeach; ?>

            </tbody>
        </table>

    <? else: ?>

        <p>Please enter a username to search for</p>

    <? endif; ?>

</div>