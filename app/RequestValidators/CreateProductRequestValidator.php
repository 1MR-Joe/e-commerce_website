<?php
declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entities\Category;
use App\Entities\Manufacturer;
use App\Exceptions\ValidationException;
use App\Services\FileService;
use Doctrine\ORM\EntityManager;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\UploadedFileInterface;
use Valitron\Validator;

class CreateProductRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly FileService $fileService,
    ){
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);

        //TODO: require photo
        $v->rule('required', ['name', 'category', 'price', 'description', 'manufacturer']);
        $v->rule('regex', ['name', 'description'], '/^[A-Za-z0-9\-._,*:\r\n\s\t()]*$/');
        //TODO: I think I need to allow more characters
        $v->rule('lengthMax', 'description', 1000);

        // price handling
        $v->rule('numeric', 'price');
        $v->rule(function($field, $value, $params, $fields) {
            $price = (float) $value;

            if($price == 0 || $price < 0) {
                return false;
            } else {
                return true;
            }
        }, "price")->message("price must be positive");

//        $price = (float) $data['price'];
//        $price *= 100;
//        $data['price'] = (int) $price;

        // category handling
        $v->rule('integer', 'category');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            /** @var Category $category */
            $category = $this->entityManager->find(Category::class, (int) $value);

            if(! $category) {
                return false;
            }

            $data['category'] = $category;
            return true;

        }, 'category')->message('category not found');

         // manufacturer handling
         $v->rule('integer', 'manufacturer');

        $v->rule(function($field, $value, $params, $fields) use(&$data) {
            /** @var Manufacturer $manufacturer */
            $manufacturer = $this->entityManager->find(Manufacturer::class, (int) $value);

            if(! $manufacturer) {
                return false;
            }

            $data['manufacturer'] = $manufacturer;
            return true;

        }, 'manufacturer')->message('manufacturer not found');

        // photo handling
        if(array_key_exists('photo', $data)) {
            /** @var UploadedFileInterface $uploadedFile */
            $file = $data['photo'];

            $this->fileService->validateFile(
                $file,
                'photo',
                5,
                '/^[a-zA-Z0-9\s._-]+$/',
                ['image/png', 'image/jpeg']
            );
        }

        if(! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}