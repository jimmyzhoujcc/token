<?php
namespace app\index\controller;
class TPDingTalk extends DingTalk
{
    /**
	 * log overwrite
	 * @see DingTalk::log()
	 */
	protected function log($log){
		if ($this->debug) {
			if (function_exists($this->logcallback)) {
				if (is_array($log)) $log = print_r($log,true);
				return call_user_func($this->logcallback,$log);
			}else {
				return true;
			}
		}
		return false;
	}
    /**
	 * 重载设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired=''){
		if($expired == ''){
			return S($cachename,$value);
		}else{
			return S($cachename,$value,$expired);
		}
	}

	/**
	 * 重载获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		$time = S($cachename.'_time');
		if(time()-$time >= 7200){
			$this->removeCache($cachename);
		}
		return S($cachename);
	}

	/**
	 * 重载清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return S($cachename,null);
	}

}