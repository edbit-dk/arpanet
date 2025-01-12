<?php

namespace Lib;

use Exception;

class Access {
    private $htaccess;
    private $email;
    private $whitelist;
    private $ip;
    private $date;
    private $uri;
    private $agent;

    public function __construct($htaccess = '.htaccess', $email = '', $whitelist = []) {
        $this->htaccess = $htaccess;
        $this->email = $email;
        $this->whitelist = $whitelist;
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->date = date('Y-m-d H:i:s');
        $this->uri = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES);
        $this->agent = str_replace(["\n", "\r"], '', htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES));
    }

    /**
     * Checks if the current IP is already banned.
     * 
     * @return bool
     */
    private function exists() {
        $contents = file_get_contents($this->htaccess, true);
        if ($contents === false) {
            throw new Exception('Unable to open .htaccess');
        }
        return stripos($contents, 'deny from ' . $this->ip . "\n") !== false;
    }

    /**
     * Adds the current IP to the .htaccess file and optionally sends an email.
     */
    private function ban() {
        // Skip if the IP is whitelisted
        if (in_array($this->ip, $this->whitelist)) {
            echo "Hello user! Because your IP address ({$this->ip}) is in our whitelist, you were not banned.";
            return;
        }

        // Append ban details to .htaccess
        $ban = "\n# The IP below was banned on {$this->date} for trying to access {$this->uri}\n";
        $ban .= "# Agent: {$this->agent}\n";
        $ban .= "Deny from {$this->ip}\n";

        if (file_put_contents($this->htaccess, $ban, FILE_APPEND) === false) {
            throw new Exception('Cannot append rule to .htaccess');
        }

        // Send email if configured
        if (!empty($this->email)) {
            $message = "IP Address: {$this->ip}\nDate/Time: {$this->date}\nUser Agent: {$this->agent}\nURL: {$this->uri}";
            mail($this->email, 'Website Auto Ban: ' . $this->ip, $message);
        }

        // Send 403 header and display an error message
        header('HTTP/1.1 403 Forbidden');
        echo "<html><head><title>Error 403 - Banned</title></head><body>
        <center><h1>Error 403 - Forbidden</h1>
        Hello user, you have been banned from accessing our site. If you feel this ban was a mistake, 
        please contact the website administrator.<br />
        <em>IP Address: {$this->ip}</em></center></body></html>";
        exit;
    }

    /**
     * Executes the auto-ban process.
     */
    public function deny() {
        // Ensure the request is not referred from another source
        if (empty($_SERVER['HTTP_REFERER'])) {
            // Check if the IP is already banned
            if ($this->exists()) {
                echo "Already banned, nothing to do here.";
                exit;
            }
            // Proceed with banning the IP
            $this->ban();
        }
    }
}
