import greenfoot.*;  // Import Greenfoot classes

public class Plane extends Actor {
    private GreenfootImage straightImage = new GreenfootImage("Plane Lurus.png");
    private GreenfootImage upImage = new GreenfootImage("Plane Naik.png");
    private GreenfootImage downImage = new GreenfootImage("Plane Turun.png");
    private GreenfootImage leftStraightImage = new GreenfootImage("Lurus Kebelakang.png");
    private GreenfootImage leftUpImage = new GreenfootImage("Ke Bekalang Naik.png");
    private GreenfootImage leftDownImage = new GreenfootImage("Ke belakang turun.png");
    private GreenfootImage explosionImage = new GreenfootImage("Explosion.png");

    private boolean isFacingRight = true;
    private boolean isFalling = false;
    private boolean hasMoved = false;
    private long idleStartTime = 0;
    private long crashTime = 0;
    private boolean isCrashed = false;
    private int crashCount = 0;
    private int energyConsumed = 0;

    private GreenfootSound startUpSound = new GreenfootSound("StartUp.mp3");
    private GreenfootSound poweringDownSound = new GreenfootSound("Powering Down.mp3");
    private GreenfootSound explosionSound = new GreenfootSound("Ledakan.mp3");
    private GreenfootSound finishSound = new GreenfootSound("Finish.mp3");
    private GreenfootSound trompetSound = new GreenfootSound("FinishTrompet.mp3");

    private int lives = 3;
    private GreenfootImage[] lifeImages = {
            new GreenfootImage("Life1.png"),
            new GreenfootImage("Life2.png"),
            new GreenfootImage("Life3.png")
        };
    private Life life;
    private ScorePanel scorePanel;
    private TopScoreWorld topScoreWorld;

    public Plane() {
        setImage(straightImage);
    }

    public void setLife(Life life) {
        this.life = life;
    }

    public void act() {
       
        if (scorePanel == null) {
            scorePanel = (ScorePanel) getWorld().getObjects(ScorePanel.class).get(0);
        }
        if (topScoreWorld == null && getWorld() instanceof TopScoreWorld) {
            topScoreWorld = (TopScoreWorld) getWorld();
        }

        if (isCrashed) {
            handleCrash();
        } else {
            handleInput();
            handleIdleFall();
            checkFinish();
            checkCollisionWithWall();
            increaseEnergy();
        }
    }
    
    private void increaseEnergy()
    {
        if (isTouching(Energy.class))
        {
            Greenfoot.playSound("eat.mp3");
            removeTouching(Energy.class);
            energyConsumed++;
            
        }
    }

    private boolean checkWallCollision(int deltaX, int deltaY) {
        int nextX = getX() + deltaX;
        int nextY = getY() + deltaY;
        setLocation(nextX, nextY); // Simulasikan pergerakan sementara

        boolean isColliding = isTouching(Wall.class); // Cek apakah ada tabrakan dengan dinding

        setLocation(getX() - deltaX, getY() - deltaY); // Kembalikan posisi semula

        return isColliding;
    }

    private void checkFinish() {
        Finish finish = (Finish) getWorld().getObjects(Finish.class).get(0);
        if (finish != null) {
            int planeCenterX = getX();
            int planeCenterY = getY();
            int finishCenterX = finish.getX();
            int finishCenterY = finish.getY();

            int deltaX = Math.abs(planeCenterX - finishCenterX);
            int deltaY = Math.abs(planeCenterY - finishCenterY);

            int finishHalfSize = 50;
            int planeHalfSize = 50;

            if (deltaX <= finishHalfSize && deltaY <= finishHalfSize) {
                finishSound.play();
                trompetSound.play();
                Greenfoot.delay(120);  // 2 detik delay sebelum pindah ke QuestionWorld
                
                Greenfoot.setWorld(new Question1World()); // Pindah ke QuestionWorld
                stopAllSounds();
            }
        }
        else if (isTouching(Finish.class) && energyConsumed == 5) 
        {
            Greenfoot.playSound("yipee.mp3");
            
            energyConsumed = 0;
            
        }
    }

    private void handleInput() {
        boolean moved = false; // Flag untuk memeriksa apakah pesawat bergerak
        int moveSpeed = 5;     // Kecepatan gerakan pesawat

        if (Greenfoot.isKeyDown("right") && Greenfoot.isKeyDown("up")) {
            isFacingRight = true;
            if (!checkWallCollision(moveSpeed, -moveSpeed)) {
                setLocation(getX() + moveSpeed, getY() - moveSpeed); // Gerak diagonal kanan atas
                setImage(upImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("right") && Greenfoot.isKeyDown("down")) {
            isFacingRight = true;
            if (!checkWallCollision(moveSpeed, moveSpeed)) {
                setLocation(getX() + moveSpeed, getY() + moveSpeed); // Gerak diagonal kanan bawah
                setImage(downImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("left") && Greenfoot.isKeyDown("up")) {
            isFacingRight = false;
            if (!checkWallCollision(-moveSpeed, -moveSpeed)) {
                setLocation(getX() - moveSpeed, getY() - moveSpeed); // Gerak diagonal kiri atas
                setImage(leftUpImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("left") && Greenfoot.isKeyDown("down")) {
            isFacingRight = false;
            if (!checkWallCollision(-moveSpeed, moveSpeed)) {
                setLocation(getX() - moveSpeed, getY() + moveSpeed); // Gerak diagonal kiri bawah
                setImage(leftDownImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("right")) {
            isFacingRight = true;
            if (!checkWallCollision(moveSpeed, 0)) {
                move(moveSpeed);
                setImage(straightImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("left")) {
            isFacingRight = false;
            if (!checkWallCollision(-moveSpeed, 0)) {
                move(-moveSpeed);
                setImage(leftStraightImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("up")) {
            if (!checkWallCollision(0, -moveSpeed)) {
                setLocation(getX(), getY() - moveSpeed);
                setImage(isFacingRight ? upImage : leftUpImage);
                moved = true;
            }
        } else if (Greenfoot.isKeyDown("down")) {
            if (!checkWallCollision(0, moveSpeed)) {
                setLocation(getX(), getY() + moveSpeed);
                setImage(isFacingRight ? downImage : leftDownImage);
                moved = true;
            }
        }

        // Menangani suara dan status idle
        if (moved && !hasMoved) {
            startUpSound.play();
            hasMoved = true;
        }

        if (moved) {
            idleStartTime = System.currentTimeMillis(); // Reset waktu idle
            if (isFalling) { 
                if (startUpSound.isPlaying()) {
                    startUpSound.stop(); // Hentikan suara yang sedang dimainkan
                }
                startUpSound.play(); // Mainkan ulang suara saat bergerak
                isFalling = false; // Tandai bahwa pesawat tidak lagi jatuh
            }

            if (poweringDownSound.isPlaying()) {
                poweringDownSound.stop(); // Hentikan suara powering down jika pesawat mulai bergerak
            }
        }
    }

    private void handleIdleFall() {
        int fallSpeed = 5; // Kecepatan jatuh (semakin besar, semakin cepat)
        if (System.currentTimeMillis() - idleStartTime >= 2000) {
            if (!isFalling) {
                poweringDownSound.play();
                isFalling = true;
            }
            // Pastikan suara terus dimainkan selama kondisi jatuh
            if (!poweringDownSound.isPlaying()) {
                poweringDownSound.play();
            }
            setImage(downImage);

            if (!checkWallCollision(0, 1)) {
                setLocation(getX(), getY() + 1);
            }
        }
    }

    private void checkCollisionWithRocket() {
        Rocket rocket = (Rocket) getOneIntersectingObject(Rocket.class);
        if (rocket != null) {
            handleCrash();
        }
    }

    private void checkCollisionWithWall() {
        if (getY() >= 490) {
            handleDefeat();
        }
    }

    public void handleDefeat() {
        if (life != null) {
            life.loseLife();
            life.updateLifeImage();
            stopAllSounds();
            if (!explosionSound.isPlaying()) {
                explosionSound.play(); // Putar suara ledakan
            }

            if (life.getRemainingLives() == 0) {
                Greenfoot.delay(120);
                Greenfoot.setWorld(new GameOverWorld());
            } else {
                setImage(explosionImage);
                explosionSound.play();
                isCrashed = true;
                crashTime = System.currentTimeMillis();
                // scorePanel.resetTimer(); // Komentari atau hapus baris ini
                Greenfoot.delay(60);
                resetPlanePosition();
            }
        }
    }

    public void resetPlanePosition() {
        setLocation(48, 317);
        setImage(straightImage);
        isCrashed = false;
    }

    public boolean getIsCrashed() {
        return isCrashed;
    }

    public void handleCrash() {
        crashCount++;

        if (!explosionSound.isPlaying()) {
            explosionSound.play();
        }

        if (crashCount == 1) {
            if (life != null && life.getRemainingLives() == 2) {
                life.updateLifeImage(); // Biarkan ini yang menangani pembaruan gambar secara otomatis
                resetPlanePosition();
            }
        } else if (crashCount == 2) {
            if (life != null && life.getRemainingLives() == 1) {
                life.updateLifeImage(); // Biarkan ini yang menangani pembaruan gambar secara otomatis
                resetPlanePosition();
            }
        } else if (crashCount == 3) {
            if (life != null && life.getRemainingLives() == 0) {
                Greenfoot.delay(120);
                Greenfoot.setWorld(new GameOverWorld()); // Game over jika nyawa habis
            }
        }

        if (!startUpSound.isPlaying()) {
            startUpSound.play();
        }
        if (!poweringDownSound.isPlaying()) {
            poweringDownSound.play();
        }

        if (scorePanel != null) {
            //scorePanel.resetTimer();
        }
    }

    public void stopAllSounds() {
        if (startUpSound.isPlaying()) {
            startUpSound.stop();
        }
        if (poweringDownSound.isPlaying()) {
            poweringDownSound.stop();
        }
        if (finishSound.isPlaying()) {
            finishSound.stop();
        }
        if (trompetSound.isPlaying()) {
            trompetSound.stop();
        }
    
        // Jangan hentikan explosionSound di sini, karena dimainkan di handleDefeat()
    }

}
