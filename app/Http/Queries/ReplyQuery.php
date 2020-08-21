<?php
namespace App\Http\Queries;

use App\Models\Reply;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedInclude;
class ReplyQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(Reply::query());

        $this->allowedIncludes('user', 'topic');
    }
}
