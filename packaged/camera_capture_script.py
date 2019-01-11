import picamera
import time

def capture(filename):
    with picamera.PiCamera() as camera:

        # -- Camera setting --#
        camera.brightness = 45
        camera.saturation = 5
        camera.contrast = 5
        camera.sharpness = 10

        # Set up white balance
        g = camera.awb_gains
        camera.awb_mode = 'auto'
        camera.awb_gains = g
        # Wait for the automatic gain control to settle

        # Define shutter speed and exposure mode
        camera.shutter_speed = camera.exposure_speed
        camera.exposure_mode = 'auto'

        # Define resolution
        camera.resolution = (3280, 2464)

        # Define capture framerate (1 for very low light, 3+ for bright sunlight
        camera.framerate = 1
        # Longer exposure time gives better image quality
        # -- Finish setting up Camera --#

        # Imaging starts
        # Stablise the camera for imaging
        camera.start_preview()
        time.sleep(10)

        # Taking an image
        camera.capture(filename)

        camera.stop_preview()

timestamp = time.localtime(time.time())
year = str(timestamp[0])
month = str(timestamp[1])
day = str(timestamp[2])
hour = str(timestamp[3])
minutes = str(timestamp[4])

capture(year + '-' + month + '-' + day + '_' + hour + '-' + minutes + '.jpg')
