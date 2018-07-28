<?php


namespace Tests\Feature\API;


use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LinkCrudTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use MakesAuthenticatedRequest;

    /**
     * @var User
     */
    private $user;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function testGetLinks()
    {
        /** @var Collection $links */
        $links = $this->createLinks($this->user);
        $linkIds = $links->pluck('id');

        $otherUser = $this->createUser();
        $this->createLinks($otherUser);

        $response = $this->makeRequest(route('api.links.getLinks'), 'GET', [], $this->user);

        $response->assertSuccessful();
        $data = $response->json('data');

        $this->assertNotNull($data);

        $this->assertArrayHasKey('links', $data);

        $responseLinks = $data['links'];
        $this->assertCount(3, $responseLinks);
        foreach ($responseLinks as $responseLink) {
            $this->assertEquals($this->user->id, $responseLink['user_id']);
            $this->assertContains($responseLink['id'], $linkIds);
        }
    }

    public function testGetLinkNotFound()
    {
        /** @var Link $link */
        $link = $this->createLink($this->user);

        //Iterate by one to get an id that doesn't exist.
        $id = $link->id + 1;

        $response = $this->makeRequest(
            route('api.links.getLink', [
                'link' => (string) $id
            ]),
            'GET',
            [],
            $this->user
        );

        $response->assertNotFound();
    }

    public function testGetLinkNotOwnedByUser()
    {
        $this->stub();
    }

    public function testGetLinkSuccess()
    {
        $this->stub();
    }

    public function testDeleteLinkNotOwnedByUser()
    {
        $this->stub();
    }

    public function testDeleteLinkSuccess()
    {
        $this->stub();
    }

    public function testUpdateLinkNotOwnedByUser()
    {
        $this->stub();
    }

    public function testUpdateLinkSuccess()
    {
        $this->stub();
    }

    public function testNewLinkValidationFailure()
    {
        $this->stub();
    }

    public function testNewLinkSuccess()
    {
        $this->stub();
    }

    public function testDeleteLinkNotFound()
    {
        $this->stub();
    }

    /**
     * Generates a single link and returns it.
     * @see LinkCrudTest::createLinks()
     *
     * @param User $user
     * @return Link
     */
    private function createLink(User $user): Link
    {
        return $this->createLinks($user, 1)
            ->first();
    }

    /**
     * Generates the specified number of links and returns them.
     *
     * @param User $user - the user to associate the links with.
     * @param int $number - the number of links to generate. Default is 3.
     * @return Collection - returns a collection of links.
     */
    private function createLinks(User $user, $number = 3): Collection
    {
        return factory(Link::class, $number)
            ->make()
            ->each(function (Link $link) use ($user) {
                $link->user()->associate($user);
                $link->save();
            });
    }
}