import { createFileRoute } from '@tanstack/react-router'
import logo from '../logo.svg'

export const Route = createFileRoute('/')({
  component: App,
})

function App() {

  return (
    <div>
      <h1>
        Welcome to Student Marketplace
      </h1>
    </div>
  )
}
