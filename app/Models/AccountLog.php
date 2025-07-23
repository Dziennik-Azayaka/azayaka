<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountLog extends BaseModel
{
    /** @use HasFactory<\Database\Factories\AccountLogFactory> */
    use HasFactory;

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
