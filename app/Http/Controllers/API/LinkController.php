<?php


namespace App\Http\Controllers\API;


use App\Models\Repository\LinkRepository;
use App\Services\LinkService;
use Illuminate\Http\Request;

class LinkController extends APIController
{

    /**
     * Retrieves a list of links.
     *
     * @method GET
     * @param Request $request
     * @param LinkRepository $repository
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getLinks(Request $request, LinkRepository $repository)
    {
        return $this->successResponse('Success', [
            'links' => $repository->getLinksForUser($request->user())
        ]);
    }

    /**
     * Saves a link.
     *
     * @method PUT
     * @param Request $request
     * @param LinkService $service
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function saveLink(Request $request, LinkService $service)
    {
        $this->validate($request, [
            'url' => 'required'
        ]);

        $link = $service->saveLink(
            $request->user(),
            $request->url,
            $request->title,
            $request->description
        );
        $link->id;

        return $this->successResponse('Success', [
            'id' => $link->id
        ]);
    }

}