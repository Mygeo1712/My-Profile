import greenfoot.*;  // (World, Actor, GreenfootImage, Greenfoot and MouseInfo)

/**
 * Write a description of class HomeWorld here.
 * 
 * @author (your name) 
 * @version (a version number or a date)
 */
public class HomeWorld extends World
{
    
    /**
     * Constructor for objects of class HomeWorld.
     * 
     */
    public HomeWorld()
    {    
        // Create a new world with 600x400 cells with a cell size of 1x1 pixels.
        super(800, 600, 1);
        setPaintOrder(
            Button_Play.class,Button_Menu.class,Button_Setting.class,Button_QuitGame.class, Button_SignIn.class,
            TopScore.class, Home.class, Cloud.class, Cloud2.class,Sun.class
        
        
        );
            SoundManager.playMusic("HomeMusic.mp3");
            SettingWorld settingWorld = new SettingWorld(this); // Mengirimkan HomeWorld sebagai previousWorl 
        GameManager.getInstance().setCurrentPlayer(new PlayerData("Nama Pemain")); // Ganti "Nama Pemain" dengan nama yang sesuai

        prepare();
    }
    
    /**
     * Prepare the world for the start of the program.
     * That is: create the initial objects and add them to the world.
     */
    private void prepare()
    {
        Home home = new Home();
        addObject(home,400,300);

        Cloud cloud = new Cloud();
        addObject(cloud,629,107);

        Cloud cloud2 = new Cloud();
        addObject(cloud2,279,101);

        Cloud2 cloud22 = new Cloud2();
        addObject(cloud22,87,58);

        Cloud2 cloud23 = new Cloud2();
        addObject(cloud23,140,355);

        Cloud2 cloud24 = new Cloud2();
        addObject(cloud24,712,33);

        Button_Play button_Play = new Button_Play();
        addObject(button_Play,383,261);

        Button_Menu button_Menu = new Button_Menu();
        addObject(button_Menu,391,361);

        Button_Setting button_Setting = new Button_Setting();
        addObject(button_Setting,232,448);

        Button_QuitGame button_Quitgame = new Button_QuitGame();
        addObject(button_Quitgame,564,448);

        TopScore topScore = new TopScore();
        addObject(topScore,389,445);

        Button_SignIn button_SignIn = new Button_SignIn();
        addObject(button_SignIn,391,484);
        
        Sun sun = new Sun();
        addObject(sun,704,38);
    }   
}
