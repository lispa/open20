<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\discussioni
 * @category   CategoryName
 */

use yii\db\Migration;
use lispa\amos\discussioni\models\DiscussioniTopic;

class m160912_145029_create_discussioni_workflow extends Migration
{
    const TABLE_WORKFLOW = '{{%sw_workflow}}';
    const TABLE_WORKFLOW_STATUS = '{{%sw_status}}';
    const TABLE_WORKFLOW_TRANSITIONS = '{{%sw_transition}}';
    
    private $fieldsToSkip = [ 'tableName', 'fieldsToCheck' ];
    private $newConfs;
    
    public function safeUp()
    {
        $this->setNewConfigurations();
        return $this->addConfs();
    }
    
    public function safeDown()
    {
        $this->setNewConfigurations();
        return $this->removeConfs();
    }
    
    /**
     * Metodo in cui settare tutte le nuove configurazioni da inserire a db.
     */
    private function setNewConfigurations()
    {
        $this->newConfs = [
            [
                'id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'initial_status_id' => 'BOZZA',
                'tableName' => self::TABLE_WORKFLOW,
                'fieldsToCheck' => ['id']
            ],
            [
                'id' => 'BOZZA',
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'label' => 'Bozza',
                'sort_order' => '1',
                'tableName' => self::TABLE_WORKFLOW_STATUS,
                'fieldsToCheck' => ['id', 'workflow_id']
            ],
            [
                'id' => 'ATTIVA',
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'label' => 'Attiva',
                'sort_order' => '2',
                'tableName' => self::TABLE_WORKFLOW_STATUS,
                'fieldsToCheck' => ['id', 'workflow_id']
            ],
            [
                'id' => 'DISATTIVA',
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'label' => 'Disattiva',
                'sort_order' => '3',
                'tableName' => self::TABLE_WORKFLOW_STATUS,
                'fieldsToCheck' => ['id', 'workflow_id']
            ],
            [
                'id' => 'DAVALIDARE',
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'label' => 'Da validare',
                'sort_order' => '4',
                'tableName' => self::TABLE_WORKFLOW_STATUS,
                'fieldsToCheck' => ['id', 'workflow_id']
            ],
            [
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'start_status_id' => 'BOZZA',
                'end_status_id' => 'DAVALIDARE',
                'tableName' => self::TABLE_WORKFLOW_TRANSITIONS,
                'fieldsToCheck' => ['workflow_id', 'start_status_id', 'end_status_id']
            ],
            [
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'start_status_id' => 'DAVALIDARE',
                'end_status_id' => 'ATTIVA',
                'tableName' => self::TABLE_WORKFLOW_TRANSITIONS,
                'fieldsToCheck' => ['workflow_id', 'start_status_id', 'end_status_id']
            ],
            [
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'start_status_id' => 'DAVALIDARE',
                'end_status_id' => 'DISATTIVA',
                'tableName' => self::TABLE_WORKFLOW_TRANSITIONS,
                'fieldsToCheck' => ['workflow_id', 'start_status_id', 'end_status_id']
            ],
            [
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'start_status_id' => 'DISATTIVA',
                'end_status_id' => 'BOZZA',
                'tableName' => self::TABLE_WORKFLOW_TRANSITIONS,
                'fieldsToCheck' => ['workflow_id', 'start_status_id', 'end_status_id']
            ],
            [
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'start_status_id' => 'ATTIVA',
                'end_status_id' => 'BOZZA',
                'tableName' => self::TABLE_WORKFLOW_TRANSITIONS,
                'fieldsToCheck' => ['workflow_id', 'start_status_id', 'end_status_id']
            ],
            [
                'workflow_id' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
                'start_status_id' => 'ATTIVA',
                'end_status_id' => 'DAVALIDARE',
                'tableName' => self::TABLE_WORKFLOW_TRANSITIONS,
                'fieldsToCheck' => ['workflow_id', 'start_status_id', 'end_status_id']
            ]
        ];
    }
    
    /**
     * Metodo che aggiunge tutte le configurazioni settate nell'array. Verifica la presenza di ciascuna e la crea se
     * non esiste già, altrimenti passa oltre.
     *
     * @return  boolean
     */
    private function addConfs()
    {
        foreach ( $this->newConfs as $newCompleteConf )
        {
            $newConf = [];
            
            if (isset($newCompleteConf['withTime']) )
            {
                $now = time();
                $newConf[ 'created_at' ] = $now;
                $newConf[ 'updated_at' ] = $now;
            }
            
            foreach ( $newCompleteConf as $fieldName => $fieldValue )
            {
                if ( !in_array($fieldName, $this->fieldsToSkip) )
                {
                    $newConf[ $fieldName ] = $fieldValue;
                }
            }
            
            $this->createConf( $newCompleteConf['tableName'], $newCompleteConf['fieldsToCheck'], $newConf );
        }
        
        return true;
    }
    
    /**
     * Metodo privato per la creazione di un singolo permesso
     *
     * @param string    $tablename      Nome tabella
     * @param array     $fieldsToCheck  Campi da verificare sulla tabella
     * @param array     $newConf        Array chiave => valore contenente i dati da inserire nella tabella.
     */
    private function createConf( $tablename, $fieldsToCheck, $newConf )
    {
        $confName = $this->composeConsoleConfName( $fieldsToCheck, $newConf );
        
        if ( $this->checkConfExist( $tablename, $fieldsToCheck, $newConf ) )
        {
            echo "Configurazione $confName per il modulo discussioni esistente. Skippo...\n";
        }
        else
        {
            $this->insert( $tablename, $newConf );
            echo "Configurazione $confName per il modulo discussioni creata.\n";
        }
    }
    
    /**
     * Ritorna il nome della configurazione da visualizzare nel messaggio in console.
     *
     * @param   array   $fieldsToCheck  Array contenente i campi da verificare.
     * @param   array   $newConf        Array contenente i valori da inserire nella tabella.
     *
     * @return  string
     */
    private function composeConsoleConfName( $fieldsToCheck, $newConf )
    {
        $confName = '';
        foreach ( $fieldsToCheck as $fieldName )
        {
            if ( strlen($confName) > 0 )
            {
                $confName .= ' - ';
            }
            $confName .= $newConf[ $fieldName ];
        }
        return $confName;
    }
    
    /**
     * Metodo per la verifica dell'esistenza del permesso in base al nome, che è univoco.
     *
     * @param   string  $tablename      Nome tabella
     * @param   array   $fieldsToCheck  Campi da verificare sulla tabella
     * @param   array   $fieldsValues   Valori della configurazione da verificare
     *
     * @return bool
     */
    private function checkConfExist( $tableName, $fieldsToCheck, $fieldsValues )
    {
        $whereCondition = "";
        foreach ( $fieldsToCheck as $fieldName )
        {
            $whereCondition .= ( (strlen($whereCondition) > 0) ? " AND " : "" ).$fieldName." LIKE '".$fieldsValues[ $fieldName ]."'";
        }
        $sql = "SELECT COUNT(*) FROM ".$tableName." WHERE ".$whereCondition;
        
        $cmd = $this->db->createCommand();
        $cmd->setSql( $sql );
        $valuesCount = $cmd->queryScalar();
        return ( $valuesCount > 0 );
    }
    
    /**
     * Metodo per la rimozione di tutte le configurazioni inserite in precedenza.
     *
     * @return  boolean
     */
    private function removeConfs()
    {
        foreach ( $this->newConfs as $newCompleteConf )
        {
            $where = "";
            foreach ( $newCompleteConf['fieldsToCheck'] as $fieldName )
            {
                $where .= ( (strlen($where) > 0) ? " AND " : "" ).$fieldName." LIKE '".$newCompleteConf[ $fieldName ]."'";
            }
            $this->delete($newCompleteConf['tableName'], $where );
        }
        
        return true;
    }
}
