<?php

namespace OCA\Machbarkeit\Migration;

use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface;

class InitData implements IRepairStep
{

    /** @var LoggerInterface */
    protected $logger;
    /** @var IConfig */
    protected $config;
    /** @var IDBConnection */
    protected $db;

    public function __construct(LoggerInterface $logger, IConfig $config, IDBConnection $db)
    {
        $this->logger = $logger;
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Returns the step's name
     */
    public function getName()
    {
        return 'Init data step!';
    }

    /**
     * @param IOutput $output
     */
    public function run(IOutput $output)
    {
        $previousVersion = $this->config->getAppValue('machbarkeit', 'installed_version', false);
        if (!$previousVersion) {
            $this->storeInitialData();
        }
    }

    protected function storeInitialData()
    {
        $this->logger->info("Loading ontology data", ["app" => "Machbarkeit"]);
        $query = $this->db->getQueryBuilder();
        $ontology = '{"children":[{"context":{"code":"Patient","display":"Patient","system":"fdpg.mii.cds","version":"1.0.0"},"display":"Gegenw\u00e4rtiges chronologisches Alter","id":"9769b42a-d305-490c-7dae-414f2d9208fa","leaf":true,"selectable":true,"termCodes":[{"code":"424144002","display":"Gegenw\u00e4rtiges chronologisches Alter","system":"http://snomed.info/sct"}]},{"context":{"code":"Patient","display":"Patient","system":"fdpg.mii.cds","version":"1.0.0"},"display":"Geschlecht","id":"a607bce8-2475-817f-895d-940eb9719457","leaf":true,"selectable":true,"termCodes":[{"code":"263495000","display":"Geschlecht","system":"http://snomed.info/sct"}]}],"context":{"code":"Person","display":"Person","system":"fdpg.mii.cds"},"display":"Person","id":"c7e18868-b7e3-cd6f-0378-67791d4e9774","leaf":false,"selectable":false,"termCodes":[{"code":"Person","display":"Person","system":"fdpg.mii.cds"}]}';
        $uiProfile = '{"Gegenwärtiges chronologisches Alter":{"attributeDefinitions":[],"name":"Gegenwärtiges chronologisches Alter","timeRestrictionAllowed":false,"valueDefinition":{"allowedUnits":[{"code":"a","display":"a","system":"http://unitsofmeasure.org","version":null},{"code":"mo","display":"mo","system":"http://unitsofmeasure.org","version":null},{"code":"wk","display":"wk","system":"http://unitsofmeasure.org","version":null},{"code":"d","display":"d","system":"http://unitsofmeasure.org","version":null}],"max":null,"min":null,"optional":false,"precision":1,"referenceCriteriaSet":null,"selectableConcepts":[],"type":"quantity"}},"Geschlecht":{"attributeDefinitions":[],"name":"Geschlecht","timeRestrictionAllowed":false,"valueDefinition":{"allowedUnits":[],"max":null,"min":null,"optional":false,"precision":1,"referenceCriteriaSet":null,"selectableConcepts":[{"code":"female","display":"Female","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"},{"code":"male","display":"Male","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"},{"code":"other","display":"Other","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"},{"code":"unknown","display":"Unknown","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"}],"type":"concept"}}}';

        $query
            ->insert('module')
            ->values(
                array(
                    'name' => $this->db->quote('Person'),
                    'ontology' => $this->db->quote($ontology),
                    'ui_profile' => $this->db->quote($uiProfile),
                    // 'ui_profile' => json_encode('{"Gegenwärtiges chronologisches Alter":{"attributeDefinitions":[],"name":"Gegenwärtiges chronologisches Alter","timeRestrictionAllowed":false,"valueDefinition":{"allowedUnits":[{"code":"a","display":"a","system":"http://unitsofmeasure.org","version":null},{"code":"mo","display":"mo","system":"http://unitsofmeasure.org","version":null},{"code":"wk","display":"wk","system":"http://unitsofmeasure.org","version":null},{"code":"d","display":"d","system":"http://unitsofmeasure.org","version":null}],"max":null,"min":null,"optional":false,"precision":1,"referenceCriteriaSet":null,"selectableConcepts":[],"type":"quantity"}},"Geschlecht":{"attributeDefinitions":[],"name":"Geschlecht","timeRestrictionAllowed":false,"valueDefinition":{"allowedUnits":[],"max":null,"min":null,"optional":false,"precision":1,"referenceCriteriaSet":null,"selectableConcepts":[{"code":"female","display":"Female","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"},{"code":"male","display":"Male","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"},{"code":"other","display":"Other","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"},{"code":"unknown","display":"Unknown","system":"http://hl7.org/fhir/administrative-gender","version":"4.0.1"}],"type":"concept"}}}')
                )

            );

        $query->executeStatement();
    }
}
