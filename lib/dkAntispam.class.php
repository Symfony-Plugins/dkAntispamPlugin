<?php

class dkAntispam
{
  static protected
    $instance = null;

  protected
    $config = null;

  static public function getInstance()
  {
    if (!self::$instance instanceof self)
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  protected function __construct()
  {
    require_once(sfConfigCache::getInstance()->checkConfig('config/antispam.yml'));

    $this->config = sfConfig::get('dk_antispam');
  }

  public function rate($text)
  {
    $score = strlen($text) > 50 ? 0 : 2;
    $score += preg_match_all('/http[s]?:\/\//', $text, $result);

    if ($score >= 20)
    {
      return 100;
    }


    foreach ($this->config['words'] as $word)
    {
      $word = str_replace(array('a','e','i','o','l','x'), array('[a4@]','[e3]','[il1]','([o0]|\(\))','[il1]','(x|><)'), $word);

      $score += 2 * preg_match_all('/'.$word.'/', $text, $result);

      if ($score >= 20)
      {
        return 100;
      }
    }

    return $score * 5;
  }
}
