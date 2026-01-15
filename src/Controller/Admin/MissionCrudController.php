<?php

namespace App\Controller\Admin;

use App\Entity\Mission;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MissionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mission::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['createdAt' => 'ASC'])
            ->setPaginatorPageSize(30)
            ->setDateTimeFormat('dd/MM/yyyy HH:mm');
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Date de création'),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description de la mission'),
            DateTimeField::new('startAt', 'Début'),
            DateTimeField::new('endAt', 'Fin'),
            AssociationField::new('skills', 'Techniques')
                ->onlyOnDetail(),
            AssociationField::new('areaLocation', 'lieu')
        ];
    }
    
}
