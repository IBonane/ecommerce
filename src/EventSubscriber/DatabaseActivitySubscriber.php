<?php

namespace App\EventSubscriber;

use App\Entity\Page;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Setting;
use App\Entity\Sliders;
use App\Entity\Category;
use Doctrine\ORM\Events;
use App\Entity\Collections;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::postRemove)]
class DatabaseActivitySubscriber
{

    private $appKernel;

    public function __construct(KernelInterface $appKernel, private EntityManagerInterface $em)
    {
       $this->appKernel = $appKernel;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postRemove,
        ];
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        $this->logActivity('create', $args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->logActivity('update', $args);
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->logActivity('remove', $args);
    }

    public function logActivity(string $action, mixed $args): void
    {
        $entity = $args->getObject();
        
        if( $entity instanceof User || 
            $entity instanceof Product ||
            $entity instanceof Category || 
            $entity instanceof Setting || 
            $entity instanceof Sliders || 
            $entity instanceof Collections ||
            $entity instanceof Page 
        ){
            switch ($action) {
                    
                case 'create':
                    $entity->setCreatedAt(new \DateTimeImmutable());
                    $entity->setUpdatedAt(new \DateTimeImmutable());
                    break;

                case 'update':
                    $entity->setUpdatedAt(new \DateTimeImmutable());
                    break;
            }
        }
        
        if($entity instanceof Product || $entity instanceof Category){
            
            switch ($action) {

                case 'remove':
                    $filenames = $entity->getImageUrls();

                    $pathFiles = $entity instanceof Product ? 'images/products': 'images/categories';
                    
                    foreach ($filenames as $filename) {
                        $this->removeFile($filename, $pathFiles);
                    }
                    break;

                case 'create':

                    $name = $entity->getName();
                    $slug = $this->slugify($name);

                    $entity->setSlug($slug);
                    break;

                case 'update':
                    $unitOfWork = $this->em->getUnitOfWork();
                    $changeSet = $unitOfWork->getEntityChangeSet($entity);

                    if (isset($changeSet['name'])) {

                        $name = $entity->getName();

                        $slug = $this->slugify($name);

                        $entity->setSlug($slug);
                    }
                    break;
            }
           
        }

        if($entity instanceof Page){
            switch ($action) {

                case 'create':

                    $title = $entity->getTitle();
                    $slug = $this->slugify($title);

                    $entity->setSlug($slug);
                    break;

                case 'update':
                    $unitOfWork = $this->em->getUnitOfWork();
                    $changeSet = $unitOfWork->getEntityChangeSet($entity);

                    if (isset($changeSet['title'])) {

                        $title = $entity->getTitle();

                        $slug = $this->slugify($title);

                        $entity->setSlug($slug);
                    }
                    break;
            }
        }
    }

    public function removeFile(string $filename, string $subFolder): void
    {

        $path = $this->appKernel->getProjectDir().'/public/assets/';
        $filePath = $path . $subFolder . '/' . $filename;

        if(file_exists($filePath)){
            unlink($filePath);
        }
    }

    public function slugify($name)
    {
        //Remplace les caractères accentués par leur équivalent non accentué
        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        //Remplace les caractères non alphabétiques par des tirets
        $name = preg_replace('/[^a-zA-Z0-9-]+/', '-', $name);
        
        // suppression des tirets en début et fin de chaîne 
        $name = trim($name, '-');

        //convertit en miuscules
        $name = strtolower($name);

        return $name;
    }
}
