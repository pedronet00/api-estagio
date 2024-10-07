<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Exception;

class PostController extends Controller
{
   
    public function index()
    {
        $posts = Post::with(['autor:id,name', 'tipo:id,tipoPost'])->get();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        try{

            // Validações título
            if(!$request->tituloPost){
                throw new Exception("O título do post não pode estar vazio!");
            }
            if(strlen($request->tituloPost) < 4){
                throw new Exception("O título do post precisa ter pelo menos 4 caracteres!");
            }
            if(strlen($request->tituloPost) > 50){
                throw new Exception("O título do post pode ter, no máximo, 50 caracteres!");
            }



            // Validações subtítulo
            if(!$request->subtituloPost){
                throw new Exception("O subtítulo do post não pode estar vazio!");
            }
            if(strlen($request->subtituloPost) < 10){
                throw new Exception("O subtítulo do post precisa ter pelo menos 10 caracteres!");
            }
            if(strlen($request->subtitulo) > 60){
                throw new Exception("O subtítulo do post pode ter, no máximo, 60 caracteres!");
            }



            if(!$request->autorPost){
                throw new Exception("O autor do post não pode estar vazio!");
            }

            // Validações data
            if(!$request->dataPost){
                throw new Exception("A data do post não pode estar vazia!");
            }
            if($request->dataPost < date("Y-m-d")){
                throw new Exception("A data do post não pode ser anterior a data de hoje!");
            }


            // Validações texto
            if(!$request->textoPost){
                throw new Exception("O texto do post não pode estar vazio!");
            }
            if(strlen($request->textoPost) < 100){
                throw new Exception("O texto do post está muito curto!");
            }
            if(strlen($request->textoPost) > 4000){
                throw new Exception("O texto do post está muito grande!");
            }


            if(!$request->imgPost){
                throw new Exception("A imagem do post não pode estar vazia!");
            }

            if(!$request->tipoPost){
                throw new Exception("O tipo do post não pode estar vazio!");
            }

            $post = Post::create([
                'tituloPost' => $request->tituloPost,
                'subtituloPost' => $request->subtituloPost,
                'autorPost' => $request->autorPost,
                'dataPost' => $request->dataPost,
                'textoPost' => $request->textoPost,
                'imgPost' => $request->imgPost,
                'tipoPost' => $request->tipoPost,
                'statusPost' => true,
                
            ]);

        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['message'=> 'Post cadastrado com sucesso!', 'post' => $post], 201);
    }

    public function show(string $id)
    {
        $post = Post::find($id);

        if(!$post){
            return response()->json(['error' => 'Post não encontrado!'], 404);
        }

        return $post;
    }

    public function update(Request $request, string $id)
    {
        try{

            $post = Post::find($id);

            if(!$post){
                return response()->json(['error' => 'Post não encontrado!'], 404);
            }

            $post->tituloPost = $request->tituloPost ?? $post->tituloPost;
            $post->subtituloPost = $request->subtituloPost?? $post->subtituloPost;
            $post->autorPost = $request->autorPost?? $post->autorPost;
            $post->dataPost = $request->dataPost?? $post->dataPost;
            $post->textoPost = $request->textoPost?? $post->textoPost;
            $post->imgPost = $request->imgPost?? $post->imgPost;
            $post->tipoPost = $request->tipoPost?? $post->tipoPost;
            $post->statusPost = $request->statusPost?? $post->statusPost;
            
            $post->save();

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao atualizar post:', 'error'=> $e->getMessage]);
        }

        return response()->json(['message' => 'Post atualizado com sucesso!', 'post' => $post], 200);

        
    }

    public function deactivate(string $id)
    {
        
        try{

            $post = Post::find($id);
            
            if(!$post){
                throw new Exception("Post não encontrado!");
            }

            if($post->statusPost == false){
                throw new Exception("Este post já está desativado!");
            }

            $post->statusPost = false;
            $post->save();

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao desativar o post', 'error'=> $e->getMessage()]);
        }

        return response()->json(['message' => 'Post desativado com sucesso!'], 200);
    }

    public function activate(string $id)
    {
        try{

            $post = Post::find($id);
            
            if(!$post){
                throw new Exception("Post não encontrado!");
            }

            if($post->statusPost == true){
                throw new Exception("Este post já está ativado!");
            }

            $post->statusPost = true;
            $post->save();

        } catch(Exception $e){
            return response()->json(['message' => 'Erro ao ativar o post', 'error'=> $e->getMessage()]);
        }

        return response()->json(['message' => 'Post ativado com sucesso!'], 200);
    }

    public function gerarRelatorioPosts()
    {
        $posts = Post::all();
        $quantidade_posts = Post::count();

        $postCounts = Post::select('autorPost', \DB::raw('count(*) as total_posts'))->groupBy('autorPost')->get();

        $contagemPorUser = $postCounts->map(function ($postCount) {
            $user = User::find($postCount->autorPost);
            return [
                'user' => $user ? $user->name : 'Usuário desconhecido',
                'total_posts' => $postCount->total_posts
            ];
        });

        return response()->json([
            'posts' => $posts,
            'quantidade_posts' => $quantidade_posts,
            'contagem_por_usuario' => $contagemPorUser
        ], 200);
    }

    public function search(Request $request){

        $query = Post::query();

        // Filtro por data específica
        if ($request->has('dataPost')) {
            $query->whereDate('dataPost', $request->input('dataPost'));
        }

        // Filtro por intervalo de datas
        if ($request->has('dataInicio') && $request->has('dataFim')) {
            $query->whereBetween('dataPost', [$request->input('dataInicio'), $request->input('dataFim')]);
        }

        // Filtro por autor do post
        if ($request->has('autorPost')) {
            $query->where('autorPost', $request->input('autorPost'));
        }

        // Filtro por tipo do post
        if ($request->has('tipoPost')) {
            $query->where('tipoPost', $request->input('tipoPost'));
        }

        // Filtro por status do post
        if ($request->has('statusPost')) {
            $query->where('statusPost', $request->input('statusPost'));
        }


        $resultados = $query->get();

        return response()->json($resultados);
    }

    public function destroy(string $id)
    {
        
    }
}
