<?php

namespace App\Models;

use Eloquent;

class LoanBorrowerDocs extends Eloquent
{
    public $timestamps = false;
    protected $table = 'loan_borrower_documents';
    protected $fillable = ['loan_id','doc_name', 'doc_type', 'timestamp'];
}
