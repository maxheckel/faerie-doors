package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"io/ioutil"
	"net/http"
	"net/url"
	"os"
	"strings"
	"sync"
	"time"
)

type Predict struct {
	Version string `json:"version"`
	Input   struct {
		Prompt     string `json:"prompt"`
		NumOutputs int    `json:"num_outputs"`
		Width      int    `json:"width"`
		Height     int    `json:"height"`
	} `json:"input"`
}

type PredictResponse struct {
	Id      string `json:"id"`
	Version string `json:"version"`
	Urls    struct {
		Get    string `json:"get"`
		Cancel string `json:"cancel"`
	} `json:"urls"`
	CreatedAt   time.Time   `json:"created_at"`
	CompletedAt interface{} `json:"completed_at"`
	Status      string      `json:"status"`
	Input       struct {
		Prompt string `json:"prompt"`
	} `json:"input"`
	Output  []string    `json:"output"`
	Error   interface{} `json:"error"`
	Logs    interface{} `json:"logs"`
	Metrics struct {
	} `json:"metrics"`
}

func main() {
	wg := &sync.WaitGroup{}
	runTimes := 20
	outImages := make(chan string, runTimes*4)
	for x := 0; x < runTimes; x++ {
		wg.Add(1)
		go getImages(wg, &outImages)
	}
	wg.Wait()
	close(outImages)

	wg = &sync.WaitGroup{}
	for val := range outImages {
		wg.Add(1)
		downloadImage(val, "outputs", wg)
	}
	wg.Wait()
}

func downloadImage(out, folderName string, wg *sync.WaitGroup) {
	defer wg.Done()
	outURL, _ := url.Parse(out)
	parts := strings.Split(outURL.Path, "/")
	fileToWrite, _ := os.Create(folderName + "/" + parts[6] + ".png")
	resp, _ := http.Get(out)
	defer resp.Body.Close()
	io.Copy(fileToWrite, resp.Body)
}

func getImages(wg *sync.WaitGroup, images *chan string) {
	defer wg.Done()
	resp := getPredictionsResponse()
	outImgs := pollPredictionResponse(resp)
	for _, url := range outImgs {
		*images <- url
	}
}

func pollPredictionResponse(predictionResp *PredictResponse) []string {
	count := 0
	for count < 60 && predictionResp.CompletedAt == nil {
		client := http.Client{}
		req, _ := http.NewRequest("GET", predictionResp.Urls.Get, nil)
		req.Header.Set("Authorization", "Token "+os.Getenv("REPLICATE_TOKEN"))
		req.Header.Set("Content-Type", "application/json")
		resp, _ := client.Do(req)
		defer resp.Body.Close()
		respBody, _ := ioutil.ReadAll(resp.Body)
		json.Unmarshal(respBody, &predictionResp)
		time.Sleep(1 * time.Second)
		fmt.Printf("waited for %d\n", count)
		count++
	}
	return predictionResp.Output
}

func getPredictionsResponse() *PredictResponse {
	client := http.Client{}
	body := Predict{
		Version: "a9758cbfbd5f3c2094457d996681af52552901775aa2d6dd0b17fd15df959bef",
		Input: struct {
			Prompt     string `json:"prompt"`
			NumOutputs int    `json:"num_outputs"`
			Width      int    `json:"width"`
			Height     int    `json:"height"`
		}{
			Prompt:     "watercolor portrait of a happy tolkien elf, facebook profile picture, photorealistic",
			NumOutputs: 4,
			Width:      512,
			Height:     768,
		},
	}

	bodyBytes, _ := json.Marshal(body)
	req, _ := http.NewRequest(http.MethodPost, "https://api.replicate.com/v1/predictions", bytes.NewBuffer(bodyBytes))
	req.Header.Set("Authorization", "Token "+os.Getenv("REPLICATE_TOKEN"))
	req.Header.Set("Content-Type", "application/json")
	resp, _ := client.Do(req)
	defer resp.Body.Close()
	respBody, _ := ioutil.ReadAll(resp.Body)
	predictResp := PredictResponse{}
	json.Unmarshal(respBody, &predictResp)
	return &predictResp
}
