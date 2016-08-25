# loginsdk
第三方登陆sdk

    
    private $app;

    public function __construct()
    {
        parent::__construct();
        
        $config = [
            'qq' => [
                'app_id' => '',
                'app_key' => '',
                'callback' => 'http://www.olcms.com/Home/Index/qqCallback'
            ]
        ];
        $this->app = new \olcms\Foundation\Application($config);
    }

    public function index()
    {
        $this->app->qq->login();
    }
    
    public function qqCallback()
    {
        $user = $this->app->qq->getUserinfo(I(''));
        dump($user);
    }
    

