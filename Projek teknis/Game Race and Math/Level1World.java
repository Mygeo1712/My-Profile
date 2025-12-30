import greenfoot.*;  // Import Greenfoot classes

public class Level1World extends World {
    
    private Life lifePanel;
    private ScorePanel scorePanel;
    private long startTime;
    private Button_Setting buttonSetting; // Tombol Setting
     
    public Level1World() {
        super(800, 600, 1);  // Membuat dunia dengan ukuran 800x600
        SoundManager.playMusic("Rasa Sayange.mp3");
        GameManager.getInstance().setCurrentLevel(1); // Simpan level saat ini
        startTime = System.currentTimeMillis();

        setPaintOrder(
            Button_Pouse.class,
            Cover1.class, Cover2.class, 
            ground.class,
            Wall.class, Rocket.class, 
            Plane.class,Finish.class,Level1.class, 
            Sun.class ,Life.class, ScorePanel.class, 
            Cloud2.class, Cloud.class

        );
        ScorePanel scorePanel = new ScorePanel();
        addObject(scorePanel, 71, 42);

        prepare();  
    }

   
    public void endLevel() {
        long endTime = System.currentTimeMillis();
        long elapsedTime = (endTime - startTime) / 1000;  // Waktu dalam detik
        GameManager.getInstance().getCurrentPlayer().setTopTime(1, elapsedTime);  // Simpan waktu level 1
        Greenfoot.setWorld(new Question1World());  // Lanjut ke Level 2
    }

    public void saveScore(long timeAlive) {
        GameManager.getInstance().setTopScore(timeAlive, 1);  // Simpan top score untuk level 1
    }

    private void resetGame() {
        removeObjects(getObjects(Plane.class));
        removeObjects(getObjects(Life.class));
        removeObjects(getObjects(ScorePanel.class));
    }

    private void prepare() {
        GameManager manager = GameManager.getInstance();
        // Menambahkan objek dengan lokasi tetap
        Sun sun = new Sun();
        addObject(sun, 700, 40);  // Posisi matahari di tengah

        Cloud cloud = new Cloud();
        addObject(cloud, 564, 186);

        Cloud2 cloud2 = new Cloud2();
        addObject(cloud2, 305, 180);

        Cloud2 cloud22 = new Cloud2();
        addObject(cloud22, 720, 279);

        Cloud2 cloud23 = new Cloud2();
        addObject(cloud23, 75, 305);

        Cloud cloud3 = new Cloud();
        addObject(cloud3, 624, 84);

        Cloud cloud4 = new Cloud();
        addObject(cloud4, 740, 82);

        Cloud cloud5 = new Cloud();
        addObject(cloud5, 315, 87);

        Cloud cloud6 = new Cloud();
        addObject(cloud6, 471, 168);

        Plane plane = new Plane();
        addObject(plane, 48,317);

        // Membuat objek Life dan menambahkannya ke dunia
        Life life = new Life();
        addObject(life, 203, 45);

        // Menghubungkan objek Plane dengan objek Life
        plane.setLife(life);

        Wall wall = new Wall();
        addObject(wall, 239, 158);

        Wall wall2 = new Wall();
        addObject(wall2, 413, 422);

        Wall wall3 = new Wall();
        addObject(wall3, 587, 153);

        Cover1 cover1 = new Cover1();
        addObject(cover1, 411, 486);

        Cover2 cover2 = new Cover2();
        addObject(cover2, 236, 81);

        Cover2 cover22 = new Cover2();
        addObject(cover22, 583, 76);

        Rocket rocket = new Rocket();
        addObject(rocket, 323, 477);

        ground ground = new ground();
        addObject(ground, 98, 518);

        ground ground2 = new ground();
        addObject(ground2, 278, 518);

        ground ground3 = new ground();
        addObject(ground3, 471, 518);

        ground ground4 = new ground();
        addObject(ground4, 666, 518);

        ground ground5 = new ground();
        addObject(ground5, 794, 518);

        Finish finish = new Finish();
        addObject(finish, 721, 272);

        Level1 level1 = new Level1();
        addObject(level1, 420, 44);

        Button_Pouse button_Pouse = new Button_Pouse();
        addObject(button_Pouse,774,22);

        Cover1 cover12 = new Cover1();
        addObject(cover12,584,230);

        Cover1 cover13 = new Cover1();
        addObject(cover13,236,230);

        Cover2 cover23 = new Cover2();
        addObject(cover23,409,340);

        Energy energy = new Energy();
        addObject(energy,394,97);

        Energy energy2 = new Energy();
        addObject(energy2,534,435);

        Energy energy3 = new Energy();
        addObject(energy3,690,82);

        Energy energy4 = new Energy();
        addObject(energy4,254,477);
        
        Energy energy5 = new Energy();
        addObject(energy5,746,452);
    }
    
       public Life getLifePanel() {
        return lifePanel;
    }

    public ScorePanel getScorePanel() {
        return scorePanel;
    }
}
