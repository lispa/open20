<?php
/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\controllers;


use lispa\amos\admin\models\UserProfile;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\CwhRegolePubblicazione;
use lispa\amos\cwh\query\CwhActiveQuery;
use lispa\amos\cwh\utility\CwhUtil;
use Yii;
use yii\web\Controller;

class CwhAjaxController extends Controller
{
    /**
     * @return string
     */
    public function actionRecipientsCheck()
    {
        if(Yii::$app->request->isAjax) {

            $this->layout = false;
            $validators = isset($_POST['validators']) ?  $_POST['validators'] : [];
            $publicationRule = $_POST['publicationRule'];
            $tagValues = $_POST['tags'];
            $scopes = $_POST['scopes'];
            $className = $_POST['className'];
            $searchName = isset($_POST['searchName']) ? $_POST['searchName'] : '';

            if(!empty($publicationRule)) {
                $publicationRuleLabel = CwhUtil::getPublicationRuleLabel($publicationRule);

                if ($publicationRule == CwhRegolePubblicazione::ALL_USERS_WITH_TAGS && empty($tagValues)) {
                    return AmosCwh::t('amoscwh', 'It is not possible to calculate recipients with rule')
                        . ' <strong>' . $publicationRuleLabel . '</strong> '
                        . AmosCwh::t('amoscwh', 'without specifying tags.');
                } elseif ($publicationRule == CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS && empty($scopes)) {
                    return AmosCwh::t('amoscwh', 'It is not possible to calculate recipients with rule')
                        . ' <strong>' .$publicationRuleLabel . '</strong> '
                        . AmosCwh::t('amoscwh', 'without specifying publication scopes.');
                } elseif ($publicationRule == CwhRegolePubblicazione::ALL_USERS_IN_DOMAINS_WITH_TAGS && (empty($tagValues) ||  empty($scopes))){
                    return AmosCwh::t('amoscwh', 'It is not possible to calculate recipients with rule')
                        . ' <strong>' .$publicationRuleLabel . '</strong> '
                        . AmosCwh::t('amoscwh', 'without specifying both tags and publication scopes.');
                }

                $cwhActiveQuery = new CwhActiveQuery($className);
                $queryUsers = $cwhActiveQuery->getRecipients($publicationRule, $tagValues, $scopes);
                $query = UserProfile::find()->andWhere([
                    'in',
                    'user_id',
                    $queryUsers->select('user.id')->asArray()->column()
                ]);

                if (!empty($searchName)) {
                    $query->andWhere(['or',
                        ['like', 'cognome', $searchName],
                        ['like', 'nome', $searchName],
                        ['like', "CONCAT( nome , ' ', cognome )", $searchName],
                        ['like', "CONCAT( cognome , ' ', nome )", $searchName],
                    ]);
                }

                return $this->render("recipients-check", [
                    'validators' => CwhUtil::getDomainNames($validators),
                    'publicationRule' => $publicationRuleLabel,
                    'tagValues' => CwhUtil::getTagNames($tagValues),
                    'scopes' => CwhUtil::getDomainNames($scopes),
                    'searchName' => $searchName,
                    'query' => $query,
                ]);
            }
        }
        return null;
    }
}