class Api extends BaseController
{
    public function materias()
    {
        $db = \Config\Database::connect();
        return $this->response->setJSON(
            $db->table('materia')->get()->getResultArray()
        );
    }
}