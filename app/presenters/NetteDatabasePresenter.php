<?php

use Grido\Components\Filters\Filter,
    Nette\Utils\Html;

/**
 * Nette\Database example.
 * @link http://doc.nette.org/en/database
 *
 * @package     Grido
 * @author      Petr Bugyík
 */
final class NetteDatabasePresenter extends BasePresenter
{
    protected function createComponentGrid($name)
    {
        $grid = new Grido\Grid($this, $name);

        $grid->setModel($this->context->ndb_sqlite->table('user'));

        $grid->addColumnText('firstname', 'Firstname')
            ->setFilterText()
                ->setSuggestion();

        $grid->addColumnText('surname', 'Surname')
            ->setSortable()
            ->setFilterText()
                ->setSuggestion();

        $grid->addColumnText('gender', 'Gender')
            ->setSortable()
            ->cellPrototype->class[] = 'center';

        $grid->addColumnDate('birthday', 'Birthday', Grido\Components\Columns\Date::FORMAT_TEXT)
            ->setSortable()
            ->setFilterDate()
                ->setCondition(Filter::CONDITION_CALLBACK, callback($this, 'gridBirthdayFilterCondition'));
        $grid->getColumn('birthday')->cellPrototype->class[] = 'center';

        $baseUri = $this->template->baseUri;
        $grid->addColumnText('country_code', 'Country')
            ->setSortable()
            ->setCustomRender(function($item) use($baseUri) {
                $img = Html::el('img')->src("$baseUri/img/flags/$item->country_code.gif");
                return "$img {$item->country->title}";
            })
            ->setFilterText()
                ->setColumn('country.title')
                ->setSuggestion(function($item){
                    return $item->country->title;
                });

        $grid->addColumnText('card', 'Card')
            ->setSortable()
            ->setColumn('cctype') //name of db column
            ->setReplacement(array('MasterCard' => Html::el('b')->setText('MasterCard')))
            ->cellPrototype->class[] = 'center';

        $grid->addColumnMail('emailaddress', 'Email')
            ->setSortable()
            ->setFilterText();
        $grid->getColumn('emailaddress')->cellPrototype->class[] = 'center';

        $grid->addColumnText('centimeters', 'Height')
            ->setSortable()
            ->setFilterNumber();
        $grid->getColumn('centimeters')->cellPrototype->class[] = 'center';

        $grid->addFilterSelect('gender', 'Gender', array(
            '' => '',
            'female' => 'female',
            'male' => 'male'
        ));

        $grid->addFilterSelect('card', 'Card', array(
                '' => '',
                'MasterCard' => 'MasterCard',
                'Visa' => 'Visa'
            ))
            ->setColumn('cctype');

        $grid->addFilterCheck('preferred', 'Only preferred girls :)')
            ->setCondition(Filter::CONDITION_CUSTOM, array(
                TRUE => 'gender = "female" AND centimeters >= 170' //for checked
        ));

        $grid->addActionHref('edit', 'Edit')
            ->setIcon('pencil');

        $grid->addActionHref('delete', 'Delete')
            ->setIcon('trash')
            ->setConfirm(function($item) {
                return "Are you sure you want to delete {$item->firstname} {$item->surname}?";
        });

        $operations = array('print' => 'Print', 'delete' => 'Delete');
        $grid->setOperations($operations, callback($this, 'gridOperationsHandler'))
            ->setConfirm('delete', 'Are you sure you want to delete %i items?');

        $grid->setFilterRenderType($this->filterRenderType);
        $grid->setExporting();
    }
}
