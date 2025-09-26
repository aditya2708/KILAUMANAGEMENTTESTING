namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $table = 'RumlapKeuangan';

    protected $fillable = ['nama', 'rumus', 'level', 'urutan', 'perent_id', ...];

    public function children()
    {
        return $this->hasMany(RumlapKeuangan::class, 'perent_id');
    }

    public function parent()
    {
        return $this->belongsTo(RumlapKeuangan::class, 'perent_id');
    }
}