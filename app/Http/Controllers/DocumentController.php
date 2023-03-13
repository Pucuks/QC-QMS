<?php

namespace App\Http\Controllers;

use DNS1D;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function documents(Request $req)
    {
        $sort   = $req->sort;
        $search = $req->q;
        $cat    = $req->category;

        $documents = DB::table('documents')
            ->leftJoin("categories", "documents.category_id", "=", "categories.category_id")
            ->select("documents.*", "categories.*");

        if (!empty($cat)) {
            $documents = $documents->orWhere([["categories.category_id", $cat]]);
        }

        if (!empty($search)) {
            $documents = $documents->orWhere([["documents.document_name", "LIKE", "%" . $search . "%"]])
                ->orWhere([["documents.title", "LIKE", "%" . $search . "%"]]);
        }

        if (empty($sort)) {
            $documents = $documents->orderBy("documents.document_id", "desc")->paginate(50);
        } else if ($sort == "desc") {
            $documents = $documents->orderBy("documents.end_date", "desc")->paginate(50);
        } else {
            $documents = $documents->orderBy("documents.end_date", "asc")->paginate(50);
        }


        return View::make("documents")->with(compact("documents"));
    }



    public function documents_save(Request $req)
    {

        $req->validate(
            [
                'document_name'      => 'required',
                'title'      => 'required',
                'start_date'    => 'required',
                'end_date'         => 'required',
                'last_update' => 'required',
                'category'          => 'required|exists:categories,category_id',
                'files' => 'mimes:docx,doc,pdf,xls,xlsx,pdf,pptx',

            ],
            [
                'document_name.required'     => 'Document Code belum diisi!',
                'title.required'     => 'title belum diisi!',
                'start_date.required'   => 'Start Date belum diisi!',
                'end_date.required'    => 'End Date belum diisi!',
                'last_update.required'       => 'Last Update belum diisi!',
                'category.required'         => 'Kategori belum dipilih!',
                'category.required'           => 'Kategori tidak tersedia!',
                'files.required'        => 'files harus berupa file!',
            ]
        );

        $data = [
            "user_id"           => Auth::user()->id,
            "document_name"      => $req->document_name,
            "title"      => $req->title,
            "start_date"      => $req->start_date,
            "end_date"    => $req->end_date,
            "last_update"        => $req->last_update,
            "category_id"       => $req->category,
            "files"       => $req->files,

        ];

        if ($req->file('files')) {
            $data['files'] = $req->file('files')->store('dokumen');
        }

        if (empty($req->id)) {
            $add = DB::table('documents')->insertGetId($data);

            if ($add) {
                $req->session()->flash('success', "Document berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Document gagal ditambahkan!");
            }
        } else {
            $update = DB::table('documents')->where("document_id", $req->id)->update($data);

            if ($update) {
                $req->session()->flash('success', "Document berhasil diubah.");
            } else {
                $req->session()->flash('error', "Document gagal diubah!");
            }
        }

        return redirect()->back();
    }


    public function documents_delete(Request $req)
    {
        $del = DB::table('documents')->where("document_id", $req->id)->delete();

        if ($del) {
            // $stock_id = DB::table('stock')->where("product_id", $req->id)->first();
            // if (!empty($stock_id)) {
            //     $stock_id = $stock_id->stock_id;
            //     DB::table('stock')->where("product_id", $req->id)->delete();
            //     DB::table('history')->where("stock_id", $stock_id)->delete();
            // }
            $req->session()->flash('success', "Product berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Product gagal dihapus!");
        }

        return redirect()->back();
    }


    public function categories(Request $req)
    {
        $search = $req->q;

        $categories = DB::table('categories')->select("*");

        if (!empty($search)) {
            $categories = $categories->where("category_name", "LIKE", "%" . $search . "%");
        }

        if ($req->format == "json") {
            $categories = $categories->get();

            return response()->json($categories);
        } else {
            $categories = $categories->paginate(50);

            return View::make("categories")->with(compact("categories"));
        }
    }

    public function categories_save(Request $req)
    {
        $category_id = $req->category_id;

        $req->validate(
            [
                'category_name'      => ['required']

            ],
            [
                'category_name.required'     => 'Nama Kategori belum diisi!',
            ]
        );

        $data = [
            "category_name"      => $req->category_name
        ];

        if (empty($category_id)) {
            $add = DB::table('categories')->insertGetId($data);

            if ($add) {
                $req->session()->flash('success', "Kategori baru berhasil ditambahkan.");
            } else {
                $req->session()->flash('error', "Kategori baru gagal ditambahkan!");
            }
        } else {
            $edit = DB::table('categories')->where("category_id", $category_id)->update($data);

            if ($edit) {
                $req->session()->flash('success', "Kategori berhasil diubah.");
            } else {
                $req->session()->flash('error', "Kategori gagal diubah!");
            }
        }

        return redirect()->back();
    }

    public function categories_delete(Request $req)
    {
        $del = DB::table('categories')->where("category_id", $req->delete_id)->delete();

        if ($del) {
            DB::table('products')->where("category_id", $req->delete_id)->update(["category_id" => null]);
            $req->session()->flash('success', "Kategori berhasil dihapus.");
        } else {
            $req->session()->flash('error', "Kategori gagal dihapus!");
        }

        return redirect()->back();
    }
}
