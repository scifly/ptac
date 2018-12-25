<?php
namespace App\Apis;

/**
 * Interface MassImport
 * @package App\Apis
 */
interface MassImport {
    
    /**
     * 验证数据
     *
     * @param array $data
     * @return mixed
     */
    function validate(array $data);
    
    /**
     * 插入数据
     *
     * @param array $inserts
     * @return mixed
     */
    function insert(array $inserts);
    
    /**
     * 更新数据
     *
     * @param array $updates
     * @return mixed
     */
    function update(array $updates);
    
}