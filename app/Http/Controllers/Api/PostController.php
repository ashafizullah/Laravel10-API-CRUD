<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
	public function index()
	{
		$posts = Post::latest()->paginate(5);
		return new PostResource('SUCCESS', 200, 'List data posts', $posts);
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'title' => 'required',
			'content' => 'required'
		]);

		if ($validator->fails()) {
			return new PostResource('ERROR', 422, 'Validation error', $validator->errors());
		}

		$image = $request->file('image');
		$image->storeAs('public/posts', $image->hashName());

		$post = Post::create([
			'title' => $request->title,
			'image' => $image->hashName(),
			'content' => $request->content
		]);

		return new PostResource('SUCCESS', 200, 'Post created successfully', $post);
	}

	public function show($id)
	{
		$post = Post::find($id);
		if ($post) {
			return new PostResource('SUCCESS', 200, 'Detail data post', $post);
		}
		return new PostResource('ERROR', 404, 'Post not found', null);
	}

	public function update(Request $request, $id)
	{
		$post = Post::find($id);

		if ($post) {
			$validator = Validator::make($request->all(), [
				'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
				'title' => 'required',
				'content' => 'required'
			]);

			if ($validator->fails()) {
				return new PostResource('ERROR', 422, 'Validation error', $validator->errors());
			}

			if ($request->hasFile('image')) {
				$image = $request->file('image');
				$image->storeAs('public/posts', $image->hashName());

				Storage::delete('public/posts/' . basename($post->image));

				$post->update([
					'image'     => $image->hashName(),
					'title'     => $request->title,
					'content'   => $request->content,
				]);
			} else {
				$post->update([
					'title' => $request->title,
					'content' => $request->content
				]);
			}

			return new PostResource('SUCCESS', 200, 'Post updated successfully', $post);
		}

		return new PostResource('ERROR', 404, 'Post not found', null);
	}

	public function destroy($id)
	{
		$post = Post::find($id);

		if ($post) {
			Storage::delete('public/posts/' . basename($post->image));
			$post->delete();
			return new PostResource('SUCCESS', 200, 'Post deleted successfully', null);
		}

		return new PostResource('ERROR', 404, 'Post not found', null);
	}
}
