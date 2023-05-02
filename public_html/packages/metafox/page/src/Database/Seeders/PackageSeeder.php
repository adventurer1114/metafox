<?php

namespace MetaFox\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use MetaFox\Page\Models\Category;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Page\Repositories\PageTypeRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PackageSeeder.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSeeder extends Seeder
{
    protected PageCategoryRepositoryInterface $categoryRepository;

    /**
     * PageTypeTableSeeder constructor.
     *
     * @param PageCategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        PageCategoryRepositoryInterface $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws ValidatorException
     */
    public function run()
    {
        $this->pageCategorySeeder();
    }

    protected array $pageCategories = [
        ['name' => 'Entertainment',],
        ['name' => 'Brand or Product'],
        ['name' => 'Local Business or Place',],
        ['name' => 'Company, Organization, or Institution'],
        ['name' => 'Artist, Band or Public Figure',],
        ['name' => 'Sports'],
        ['name' => 'Food'],
        ['name' => 'Travel'],
        ['name' => 'Photography'],
        ['parent_id' => '1', 'name' => 'Album', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Amateur Sports Team', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Book', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Book Store', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Concert Tour', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Concert Venue', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Fictional Character', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Library', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Magazine', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Movie', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Movie Theater', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Music Award', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Music Chart', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Music Video', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Musical Instrument', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Playlist', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Professional Sports Team', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Radio Station', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Record Label', 'level' => 2],
        ['parent_id' => '1', 'name' => 'School Sports Team', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Song', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Sports League', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Sports Venue', 'level' => 2],
        ['parent_id' => '1', 'name' => 'Studio', 'level' => 2],
        ['parent_id' => '1', 'name' => 'TV Channel', 'level' => 2],
        ['parent_id' => '1', 'name' => 'TV Network', 'level' => 2],
        ['parent_id' => '1', 'name' => 'TV Show', 'level' => 2],
        ['parent_id' => '1', 'name' => 'TV/Movie Award', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Appliances', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Baby Goods/Kids Goods', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Bags/Luggage', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Building Materials', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Camera/Photo', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Cars', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Clothing', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Commercial Equipment', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Computers', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Drugs', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Electronics', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Food/Beverages', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Furniture', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Games/Toys', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Health/Beauty', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Home Decor', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Household Supplies', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Jewelry/Watches', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Kitchen/Cooking', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Movies/Music', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Musical Instrument', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Office Supplies', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Outdoor Gear/Sporting Goods', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Patio/Garden', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Pet Supplies', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Product/Service', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Software', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Tools/Equipment', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Vitamins/Supplements', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Website', 'level' => 2],
        ['parent_id' => '2', 'name' => 'Wine/Spirits', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Airport', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Arts/Entertainment/Nightlife', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Attractions/Things to Do', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Automotive', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Bank/Financial Services', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Bar', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Book Store', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Business Services', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Church/Religious Organization', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Club', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Community/Government', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Concert Venue', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Education', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Event Planning/Event Services', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Food/Grocery', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Health/Medical/Pharmacy', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Home Improvement', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Hospital/Clinic', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Hotel', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Landmark', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Library', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Local Business', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Movie Theater', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Museum/Art Gallery', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Pet Services', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Professional Services', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Public Places', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Real Estate', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Restaurant/Cafe', 'level' => 2],
        ['parent_id' => '3', 'name' => 'School', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Shopping/Retail', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Spas/Beauty/Personal Care', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Sports Venue', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Sports/Recreation/Activities', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Tours/Sightseeing', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Transit Stop', 'level' => 2],
        ['parent_id' => '3', 'name' => 'Transportation', 'level' => 2],
        ['parent_id' => '3', 'name' => 'University', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Aerospace/Defense', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Automobiles and Parts', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Bank/Financial Institution', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Biotechnology', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Cause', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Chemicals', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Church/Religious Organization', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Community Organization', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Company', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Computers/Technology', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Consulting/Business Services', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Education', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Energy/Utility', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Engineering/Construction', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Farming/Agriculture', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Food/Beverages', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Government Organization', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Health/Beauty', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Health/Medical/Pharmaceuticals', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Industrials', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Insurance Company', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Internet/Software', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Legal/Law', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Media/News/Publishing', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Mining/Materials', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Non-Governmental Organization (NGO)', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Non-Profit Organization', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Organization', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Political Organization', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Political Party', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Retail and Consumer Merchandise', 'level' => 2],
        ['parent_id' => '4', 'name' => 'School', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Small Business', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Telecommunication', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Transport/Freight', 'level' => 2],
        ['parent_id' => '4', 'name' => 'Travel/Leisure', 'level' => 2],
        ['parent_id' => '4', 'name' => 'University', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Actor/Director', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Artist', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Athlete', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Author', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Business Person', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Chef', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Coach', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Comedian', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Dancer', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Doctor', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Editor', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Entertainer', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Fictional Character', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Government Official', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Journalist', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Lawyer', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Monarch', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Musician/Band', 'level' => 2],
        ['parent_id' => '5', 'name' => 'News Personality', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Politician', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Producer', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Public Figure', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Teacher', 'level' => 2],
        ['parent_id' => '5', 'name' => 'Writer', 'level' => 2],
    ];

    /**
     * @throws ValidatorException
     */
    public function pageCategorySeeder()
    {
        if (Category::query()->exists()) {
            return;
        }
        foreach ($this->pageCategories as $item) {
            $this->categoryRepository->create($item);
        }
    }
}
