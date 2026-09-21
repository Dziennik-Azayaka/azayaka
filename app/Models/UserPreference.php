<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
	protected $fillable = ["key", "value", "user_id"];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
